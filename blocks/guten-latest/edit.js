import classnames from 'classnames';
import Theme from './theme.json';
import {
    InspectorControls,
    useBlockProps,
} from '@wordpress/block-editor';
import {
    PanelBody,
    ColorPalette,
    SelectControl,
} from '@wordpress/components';

import {
    Fragment,
} from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import GetTerm from './EditPart/js/GetTerm';
import './EditPart/css/admin/editor.scss';
const TAXONOMY_Data = {
    slug: '',
    terms: [],
    operator: 'IN',
};
export default function GutenLatest( props ) {
    const { attributes, setAttributes } = props;
    const {
        taxonomies,
        selectedColor,
    } = attributes;
    /**
     * Get Colors From Theme.json
     */
    const colors = Theme.settings.color.palette
    /**
     * A Callback for Color Change in Palette in Inspector
     */
    function onChangeColor(newColor) {
        setAttributes({ selectedColor: newColor });
    }

    /**
     * Define Initial Value For Terms
     * in future we can refactor this easily to be a setting in inspector
     */
    const taxData = [
        { label: "هیچ‌کدام", value: "" },
        { label: "دسته‌ها", value: "category,categories" }

    ];


    /**
     * Define the Global BlockProps of this Block
     */
    const LatestGutenProps = useBlockProps( {
        className: classnames( {
            'wp-block-latest-posts': true,
        } ),
    } );

    /**
     * Update The Category Selected in taxonomySetting Function
     */
    const ReNewTaxs = ( index, property, value ) => {
        let TaxUpdate;
        if ( taxonomies?.length ) {
            TaxUpdate = Object.values( {
                ...taxonomies,
                [ index ]: {
                    ...taxonomies[ index ],
                    [ property ]: value,
                },
            } );
        }
        if ( 'slug' === property ) {
            if ( ! taxonomies?.length ) {
                TaxUpdate = [
                    {
                        slug: value,
                        terms: [],
                        operator: 'IN',
                    },
                ];
            } else if ( value !== taxonomies[ index ].slug ) {
                TaxUpdate = Object.values( {
                    ...TaxUpdate,
                    [ index ]: {
                        ...TaxUpdate[ index ],
                        terms: [],
                        operator: undefined,
                    },
                } );
            }
        }
        if ( 'terms' === property ) {
            const operatorValue = ! taxonomies[ index ].operator
                ? 'IN'
                : taxonomies[ index ].operator;

            TaxUpdate = Object.values( {
                ...TaxUpdate,
                [ index ]: {
                    ...TaxUpdate[ index ],
                    operator: operatorValue,
                },
            } );
        }
        return TaxUpdate;
    };


    /**
     * The Setting Of Taxonomy And Which Category is Selected
     */
    const taxonomySetting = ( taxonomy, index ) => {
        return (
            <div >
                <div >
                    <SelectControl
                        label={ __( 'Taxonomy' ) }
                        value={ taxonomy.slug ? taxonomy.slug : '' }
                        options={ taxData }
                        onChange={ ( value ) => {
                            if ( '' === value ) {
                                if ( 1 === taxonomies.length ) {
                                    setAttributes( { taxonomies: [] } );
                                } else {
                                    taxonomies.splice( index, 1 );

                                    setAttributes( {
                                        taxonomies: [ ...taxonomies ],
                                    } );
                                }
                            } else {
                                setAttributes( {
                                    taxonomies: ReNewTaxs(
                                        index,
                                        'slug',
                                        value
                                    ),
                                } );
                            }
                        } }
                    />
                    { taxonomy.slug !== '' && (
                        <GetTerm
                            onChange={ ( value ) =>
                                setAttributes( {
                                    taxonomies: ReNewTaxs(
                                        index,
                                        'terms',
                                        value
                                    ),

                                } )

                            }
                            ChoosedTerms={ taxonomy.terms }
                            taxonomy={ taxonomy.slug }
                        />
                    )
                    }
                </div>
            </div>
        );
    };



    /**
     * The Block Options Panel
     */
    const GutenControll = (
        <InspectorControls>
            <PanelBody
                title={ __( 'Block Filters' ) }

            >
                <div >
                    <p>{ __( 'Taxonomies' ) }</p>
                    { 0 < taxonomies?.length
                        ? taxonomies.map( ( taxonomy, index ) =>
                            taxonomySetting( taxonomy, index )
                        )
                        : taxonomySetting( TAXONOMY_Data, 0 ) }

                </div>

            </PanelBody>
            <PanelBody
                title={ __( 'Theme' ) }
            >
                <ColorPalette
                    label={ __( 'رنگ از روی پالت انتخابی' ) }
                    colors={ colors }
                    value={ selectedColor }
                    onChange={onChangeColor}
                />

            </PanelBody>
        </InspectorControls>
    );

    /**
     * Render This Edit Options in Admin Inspector
     */
    return (
        <Fragment>
            { GutenControll }
            {/*here { ...LatestGutenProps } will be pass to in admin the inspector works good*/}
            <ul { ...LatestGutenProps }>
                <p>publish page and see posts</p>
            </ul>
        </Fragment>
    );
}
