/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    Button,
    __experimentalText as Text,
    TextControl,
    CardFooter,
    CardBody,
    Card,
    Notice
} from '@wordpress/components';
import { useState } from 'react';
import TinyMCE from './Fields/TinyMCE';
import apiFetch from '@wordpress/api-fetch';

/**
 * Handles the display of the welcome step of the wizard.
 */
const ContentStep = ( { goToNextStep, goToPreviousStep, nextButtonLabel } ) => {

    const [ tabTitle, setTabTitle ] = useState( '' );
    const [ tabContent, setTabContent ] = useState( '' );
    const [ noticeContent, setNoticeContent ] = useState( '' );
    const [ noticeClass, setNoticeClass ] = useState( 'hidden' );
    const [ buttonBusy, setButtonBusy ] = useState( false );

    const handleSkipButton = function() {
        goToNextStep();
    }

    const handleNextButton = function() {
        setNoticeClass( 'hidden' );
        setButtonBusy( true );
        if( !tabTitle || !tabContent ) {
            setNoticeContent( 'The title or content cannot be empty' );
            setButtonBusy( false );
            setNoticeClass( 'error' );
            return;
        }

        apiFetch( {
            path: '/wp/v2/woo_product_tab',
            method: 'POST',
            data: {
                title: tabTitle,
                content: tabContent,
                status: 'publish',
            },
        } ).then( ( res ) => {
            goToNextStep();
        } );
        
    }

    return (
        <Card>
            <CardBody>
                <TextControl 
                label="Tab title"
                value={ tabTitle }
                onChange={ ( value ) => setTabTitle( value ) }
                className="barn2-wizard-input"
                />
                <Text className="barn2-form-label">Tab content</Text>
                <TinyMCE value={ tabContent } onChange={ setTabContent } />
                <Notice isDismissible={ false } status={noticeClass}>{noticeContent}</Notice>
            </CardBody>
            <CardFooter>
                <Button
                    className="skip-button"
                    isSecondary
                    onClick={ () => handleSkipButton() }
                >
                    { __( 'Skip' ) }
                </Button>
                <Button
                    isPrimary
                    isBusy={ buttonBusy }
                    onClick={ () => handleNextButton() }
                >
                    { __( 'Create' ) }
                </Button>
            </CardFooter>
        </Card>
    )

}

export default ContentStep