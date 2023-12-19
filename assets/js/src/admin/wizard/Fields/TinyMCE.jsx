/**
 * External dependencies
 */
import { useRef, useState, useEffect, useCallback } from '@wordpress/element';
import { nanoid } from 'nanoid';

/**
 * TinyMCE field.
 *
 * @param {Object} props          - Component props.
 * @param          props.setting  - The setting object.
 * @param          props.value    - The current value.
 * @param          props.onChange - The change handler.
 * @return {JSX} Returns the TinyMCE field.
 */
const TinyMCE = ( { setting, value, onChange } ) => {
	const el = useRef();
	const editor = useRef( null );
	const [ editorId, _ ] = useState( nanoid() );
  
	const listener = useCallback( () => onChange( wp.editor.getContent( editorId ) ), [ editorId ] );

	useEffect( () => {
		wp.editor.initialize( editorId, {
			quicktags: false,
			mediaButtons: false,
			tinymce: {
				toolbar1:
					'formatselect,styleselect,bold,italic,bullist,numlist,link,alignleft,aligncenter,alignright,wp_adv',
				toolbar2: 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
				style_formats_merge: true,
				style_formats: [],
			},
		} );

		setTimeout(
			() => window.tinymce.editors[ editorId ] && window.tinymce.editors[ editorId ].on( 'change', listener )
		);

		return () => {
			if ( ! window.tinymce.editors[ editorId ] ) return;

			setTimeout( () => {
				window.tinymce.editors[ editorId ].off( 'change', listener );
				wp.editor.remove( editorId );
			}, 300 );
		};
	}, [] );

	return (
		<div className="ct-option-editor">
			<textarea
				style={ { opacity: 0 } }
				id={ editorId }
				ref={ el }
				value={ value }
				className="wp-editor-area"
				onChange={ ( { target: { value } } ) => onChange( value ) }
			/>
		</div>
	);
};

export default TinyMCE;
