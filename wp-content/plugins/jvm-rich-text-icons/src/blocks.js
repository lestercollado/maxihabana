/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { registerFormatType } = wp.richText;
const { Fragment } = wp.element;
const { Toolbar, Button, Popover } = wp.components;

// For the single icon blok
const { registerBlockType } = wp.blocks; 
const { InspectorControls } = wp.blockEditor;
const { PanelBody } = wp.components;
const { ComboboxControl } = wp.components;

//import icon from './icon';

import IconMap from './controls';

/**
 * Block constants
 */
const name = 'jvm/insert-icons';


export const icon = {
    name,
    title: __('Insert Icon', 'jvm-richtext-icons'),
    tagName: 'i',
    className: null,
    edit( { isOpen, value, onChange, activeAttributes } ) {
        return (
            <Fragment>
                <IconMap
                    name={ name }
                    isOpen={ isOpen }
                    value={ value }
                    onChange={ onChange }
                    activeAttributes={ activeAttributes }
                />
            </Fragment>
        );
    }
};


// Register the icon button
wp.domReady(function(){
    [
        icon,
    ].forEach( ( { name, ...settings } ) => {
        if ( name ) {
            registerFormatType( name, settings );
        }
    } );   
});

registerBlockType( 'jvm/single-icon', {
    // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
    title: __( 'Single icon' ), // Block title.
    icon: 'flag', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
    keywords: [
        __( 'Icon' )
    ],


    attributes: {
        icon: {
            type: 'string'
        },
    },

    /**
     * The edit function describes the structure of your block in the context of the editor.
     * This represents what the editor will render when the block is used.
     *
     * The "edit" property must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     *
     * @param {Object} props Props.
     * @returns {Mixed} JSX Component.
     */
    edit: ( props ) => {
        let icons = jvm_richtext_icon_settings.iconset;
        let options = [];
        let selectectValue = '';
        let classPrefix = jvm_richtext_icon_settings.base_class

        for (let icon of icons) {
            options.push({
                value : icon,
                label : icon
            });
        }

        // Get the current or first icon
        if (props.attributes.icon !== undefined) {
            selectectValue = props.attributes.icon;
        }else {
            if (icons[0] !== undefined) {
                selectectValue = icons[0];
            }
        }

        // Update the proerties
        props.setAttributes( { icon: selectectValue } );

        let cssClass = classPrefix + ' '+props.attributes.icon;

        return [
            <InspectorControls>
                <PanelBody
                    label={__( 'Icon' )}
                >
                    <ComboboxControl
                        label={__('Icon')}
                        value={selectectValue}
                        onChange={(i) => {
                            if (i) {
                                props.setAttributes( { icon: i } );
                            }
                        }}
                        options={options}
                        __experimentalRenderItem={(opt) => {
                            let cssClass = classPrefix + ' '+opt.item.value;
                            return <span><i class={cssClass} aria-hidden="true"> </i> {opt.item.value}</span>;
                        }}
            isMulti='false'
                        />
                </PanelBody>

            </InspectorControls>
            ,
            
            <div className={ props.className }>
                <i class={cssClass} aria-hidden="true"> </i>
            </div>
        ];
    },

    /**
     * The save function defines the way in which the different attributes should be combined
     * into the final markup, which is then serialized by Gutenberg into post_content.
     *
     * The "save" property must be specified and must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     *
     * @param {Object} props Props.
     * @returns {Mixed} JSX Frontend HTML.
     */
    save: ( props ) => {
        let classPrefix = jvm_richtext_icon_settings.base_class;
        let cssClass = classPrefix + ' '+props.attributes.icon;
        return (
            <div className={ props.className }>
               <i class={cssClass} aria-hidden="true"> </i>
            </div>
        );
    },
} );