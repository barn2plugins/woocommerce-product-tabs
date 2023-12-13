'use strict';

( function( $ ) {
  //Accordion
  const acc = document.getElementsByClassName( 'wpt_accordion' );
  if ( acc ) {
    let i;
    for ( i = 0; i < acc.length; i++ ) {
      const panel = acc[ i ].nextElementSibling;
      if( ! panel.querySelector( '.override-tab-content' ).checked ) {
        panel.querySelector( '.wp-editor-wrap' ).classList.add( 'hidden' );
      }
      acc[ i ].addEventListener( 'click', function() {
        this.classList.toggle( 'active' );
        panel.classList.toggle( 'hidden' );
      } );
    }
  }

  // Show the editor field
  const overrideInputs = $( '.override-tab-content' );
  if( overrideInputs ) {
    overrideInputs.each( function( i ) {
      let editor = $( this ).parents('.tab-container').find( '.wp-editor-wrap' );
      $( this ).on( 'change', function( e ) {
        editor.toggleClass( 'hidden' );
      })
    } )
  };

}( jQuery ) );