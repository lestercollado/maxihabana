/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { getRectangleFromRange } = wp.dom;
const { compose, ifCondition } = wp.compose;
const { withSelect } = wp.data;
const { BlockControls} = wp.blockEditor;
const { toggleFormat, insert, create } = wp.richText;
const { ToolbarGroup, Popover, Panel, ToolbarButton, Button, TextControl, Tooltip } = wp.components;

let Icons = jvm_richtext_icon_settings.iconset;
let classPrefix = jvm_richtext_icon_settings.base_class;

class IconMap extends Component {
    constructor() {
        super( ...arguments );

        this.toggle = this.toggle.bind( this );

        this.state = {
            icons: Icons,
            isOpen: false,
            keyword: '',
        };
    }

    search( keyword ) {
        let filtered = [];

        for (let icon of Icons) {    
            if ( icon.toLowerCase().search(
                keyword.toLowerCase() ) !== -1 ) {
                filtered.push(icon);
            }
        }

        this.setState( { keyword, icons: filtered } );
    }

    toggle() {
        this.setState( ( state ) => ( {
            isOpen: ! state.isOpen,
        } ) );

        this.setState( {  keyword: '', icons: Icons } );

        //const selection = window.getSelection();
        //anchorRange = selection.rangeCount > 0 ? selection.getRangeAt( 0 ) : null;
        //onChange( toggleFormat( value, { type: name } ) );
    }

    render() {
        const { isOpen, icons, keyword } = this.state;
        const { name, value, onChange } = this.props;
    

        return (
            <Fragment>
                <BlockControls>
                    <ToolbarGroup>
                        <ToolbarButton
                            icon={ "flag" }
                            aria-haspopup="true"
                            label={ __('Insert Icon', 'jvm-richtext-icons') }
                            onClick={ this.toggle }
                        >
                        </ToolbarButton>
                   
                        { isOpen && (
                            <Popover
                                className="jvm-richtext-icons-popover"
                                position="bottom left"
                                key="icon-popover"
                                onClick={ () => {} }
                                
                                expandOnMobile={ false }
                                headerTitle={ __( 'Insert Icon', 'jvm-richtext-icons' ) }
                                onClose={ () => {
                                    onChange( toggleFormat( value, { type: name } ) );
                                } }
                            >
                                <TextControl
                                    value={ keyword }
                                    placeholder={ __( 'Search', 'jvm-richtext-icons' ) }
                                    onChange={ ( newKeyword ) => {
                                        this.search( newKeyword );
                                    } }
                                />
                                <div
                                    className="jvm-richtext-icons-panel"
                                >
                                   
                                    { icons.length > 0 ? (
                                        <ul className="jvm-richtext-icons-list">
                                            { 
                                                //for (let icon of icons) {   
                                                icons.map(( icon ) => {
                                                return (
                                                    <li data-key={ icon }>
                                                        <Tooltip text={icon}>
                                                            <Button
                                                                isTertiary
                                                                onClick={ () => {
                                                                    let temp = create({'html' : '<i class="'+classPrefix+' '+icon+'" aria-hidden="true"> </i>'});

                                                                    onChange( insert( value,  temp ) );
                                                                    
                                                                    this.toggle();
                                                                } }
                                                            >
                                                                <i className={ classPrefix + ' ' + icon } aria-hidden="true"></i>
                                                            </Button>
                                                        </Tooltip>
                                                    </li>
                                                );
                                            } ) }
                                        </ul>
                                    ) : (
                                        <p>{ __( 'No characters found.', 'block-options' ) }</p>
                                    ) }
                                </div>
                            </Popover>
                        ) }
                        

                        </ToolbarGroup>
                    </BlockControls>
                </Fragment>
        );
    }
}

export default compose()( IconMap );
