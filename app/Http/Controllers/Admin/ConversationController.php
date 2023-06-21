<?php

namespace HelpGent\App\Http\Controllers\Admin;

use Exception;
use HelpGent\App\DTO\ConversationDTO;
use HelpGent\App\Http\Controllers\Controller;
use HelpGent\App\Repositories\ConversationRepository;
use HelpGent\WaxFramework\RequestValidator\Validator;
use HelpGent\WaxFramework\Routing\Response;
use WP_REST_Request;

class ConversationController extends Controller {
    public ConversationRepository $conversation_repository;

    public function __construct( ConversationRepository $conversation_repository ) {
        $this->conversation_repository = $conversation_repository;
    }

    public function index( Validator $validator, WP_REST_Request $wp_rest_request ) {
        $validator->validate(
            [
                'submission_id' => 'required|numeric',
                'per_page'      => 'numeric',
                'page'          => 'numeric',
                'search'        => 'string'
            ]
        );

        if ( $validator->is_fail() ) {
            return Response::send(
                [
                    'messages' => $validator->errors
                ], 422
            );
        }

        return Response::send(
            $this->conversation_repository->get( 
                intval( $wp_rest_request->get_param( 'submission_id' ) ), 
                intval( $wp_rest_request->get_param( 'per_page' ) ), 
                intval( $wp_rest_request->get_param( 'page' ) ), 
                (string) $wp_rest_request->get_param( 'search' ) 
            )
        );
    }

    public function store( Validator $validator, WP_REST_Request $wp_rest_request ) {
        $validator->validate(
            [
                'submission_id' => 'required|numeric',
                'message'       => 'required|string',
                'is_attachment' => 'required|integer|accepted:0,1',
                'parent_id'     => 'integer|min:1',
                'parent_type'   => 'string|accepted:reply,forward'
            ]
        );

        if ( $validator->is_fail() ) {
            return Response::send(
                [
                    'messages' => $validator->errors
                ], 422
            );
        }

        /**
         * Attachment request validation
         */
        $is_attachment = intval( $wp_rest_request->get_param( 'is_attachment' ) );
        $message       = $wp_rest_request->get_param( 'message' );

        if ( 1 === $is_attachment && ! is_numeric( $message ) ) {
            return Response::send(
                [
                    'messages' => [
                        'message' => [
                            "The message must be a number."
                        ]
                    ]
                ], 422
            );
        }

        /**
         * Parent request validation
         */

        $parent_type = $wp_rest_request->get_param( 'parent_type' );
        
        if ( $wp_rest_request->has_param( 'parent_id' ) && empty( $parent_type ) ) {
            return Response::send(
                [
                    'messages' => [
                        'parent_type' => [
                            "The parent_type field is required."
                        ]
                    ]
                ], 422
            );
        }

        $conversation_dto = new ConversationDTO(
            intval( $wp_rest_request->get_param( 'submission_id' ) ),
            $message,
            get_current_user_id(),
            $is_attachment,
            0,
            intval( $wp_rest_request->get_param( 'parent_id' ) ),
            $parent_type
        );

        try {
            do_action( 'helpgent_before_store_conversation', $conversation_dto, $wp_rest_request );
            
            $conversation_id = $this->conversation_repository->create( $conversation_dto );

            $conversation_dto->set_id( $conversation_id );

            do_action( 'helpgent_after_store_conversation', $conversation_dto, $wp_rest_request );
            return Response::send( [], 201 );
        } catch ( Exception $exception ) {
            return Response::send(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }
    }

    public function update( Validator $validator, WP_REST_Request $wp_rest_request ) {
        $validator->validate(
            [
                'id'            => 'required|numeric',
                'submission_id' => 'required|numeric',
                'message'       => 'required|string'
            ]
        );

        if ( $validator->is_fail() ) {
            return Response::send(
                [
                    'messages' => $validator->errors
                ], 422
            );
        }

        $message = $wp_rest_request->get_param( 'message' );

        $conversation_dto = new ConversationDTO(
            intval( $wp_rest_request->get_param( 'submission_id' ) ),
            $message
        );

        $conversation_dto->set_id( intval( $wp_rest_request->get_param( 'id' ) ) );
        try {
            do_action( 'helpgent_before_update_conversation', $conversation_dto, $wp_rest_request );

            $this->conversation_repository->update( $conversation_dto );

            do_action( 'helpgent_before_update_conversation', $conversation_dto, $wp_rest_request );
            return Response::send( [] );
        } catch ( Exception $exception ) {
            return Response::send(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }
    }

    public function delete( Validator $validator, WP_REST_Request $wp_rest_request ) {
        $validator->validate(
            [
                'id'            => 'required|numeric',
                'submission_id' => 'required|numeric'
            ]
        );

        if ( $validator->is_fail() ) {
            return Response::send(
                [
                    'messages' => $validator->errors
                ], 422
            );
        }

        try {
            $this->conversation_repository->delete( intval( $wp_rest_request->get_param( 'id' ) ), intval( $wp_rest_request->get_param( 'submission_id' ) ) );
            return Response::send( [] );
        } catch ( Exception $exception ) {
            return Response::send(
                [
                    'message' => $exception->getMessage()
                ],
                $exception->getCode()
            );
        }
    }

    public function attachment( Validator $validator, WP_REST_Request $wp_rest_request ) {
        $validator->validate(
            [
                'type'          => 'required|string',
                'submission_id' => 'required|numeric'
            ]
        );
    
        if ( $validator->is_fail() ) {
            return Response::send(
                [
                    'messages' => $validator->errors
                ], 422
            );
        }

        return Response::send(
            $this->conversation_repository->attachment( 
                intval( $wp_rest_request->get_param( 'submission_id' ) ), 
                $wp_rest_request->get_param( 'type' ),
                intval( $wp_rest_request->get_param( 'per_page' ) ), 
                intval( $wp_rest_request->get_param( 'page' ) )
            )
        );
    }
}