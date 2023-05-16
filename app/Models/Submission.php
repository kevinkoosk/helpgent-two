<?php

namespace HelpGent\App\Models;

use HelpGent\WaxFramework\App;
use HelpGent\WaxFramework\Database\Resolver;
use HelpGent\WaxFramework\Database\Eloquent\Model;
use HelpGent\WaxFramework\Database\Eloquent\Relations\HasOne;

class Submission extends Model {
    public static function get_table_name():string {
        return 'helpgent_submissions';
    }

    public function conversation():HasOne {
        return $this->has_one( Conversation::class, 'submission_id', 'id' );
    }

    public function resolver():Resolver {
        return App::$container->get( Resolver::class );
    }
}