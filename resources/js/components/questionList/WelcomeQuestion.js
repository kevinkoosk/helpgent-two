import ReactQuill from 'react-quill';
import ReactSVG from 'react-inlinesvg';
import clock from '@icon/clock.svg';
import 'react-quill/dist/quill.snow.css';
export default function WelcomeQuestion( props ) {
	const { layoutMode, singleForm, setSingleForm } = props;
	const { content } = singleForm;
	const { questions } = JSON.parse( content );
	const welcomeQuestion = questions.filter(
		( item ) => item.screen_type === 'welcome'
	);
	const fieldElements = welcomeQuestion[ 0 ].fields[ 0 ].elements;

	const quillModules = {
		toolbar: false,
	};

	let greetText = 'Type your greeting text here!*';
	let description = '';

	for ( const element of fieldElements ) {
		if ( 'greeting_text' in element ) {
			greetText = element.greeting_text;
			break;
		} else if ( 'description' in element ) {
			description = element.description;
			break;
		}
	}

	return (
		<div className="helpgent-question-element">
			<div className="helpgent-question-element__text">
				<div className="helpgent-question-element__label">
					<ReactQuill
						modules={ quillModules }
						placeholder="Type your greeting text here!*"
					/>
				</div>
				<div className="helpgent-question-element__description">
					<ReactQuill
						modules={ quillModules }
						placeholder="Type description here!*"
					/>
				</div>
			</div>
			<div className="helpgent-question-element__action helpgent-mt-20">
				<button className="helpgent-btn-start helpgent-btn helpgent-btn-primary helpgent-btn-md">
					Start
				</button>
				<div className="helpgent-question-time">
					<ReactSVG src={ clock } />
					<span>Take 10 minutes</span>
				</div>
			</div>
		</div>
	);
}