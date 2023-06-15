import apiFetch from '@wordpress/api-fetch';
import { SelectControl } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { useEffect, useState, useRef } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';
export default function GetTerm( props ) {
	const { onChange, ChoosedTerms, taxonomy } = props;
	const [ termsData, setTermsData ] = useState( [] );
	const Mounted = useRef();
	const restSlug = taxonomy.split( ',' )[ 1 ];
	useEffect( () => {
		Mounted.current = true;
		apiFetch( {
			path: addQueryArgs( `/wp/v2/${ restSlug }`, {
				per_page: -1,
			} ),
		} )
			.then( ( data ) => {
				if ( Mounted.current ) {
					const termData = data.map( ( term ) => {
						return {
							label: decodeEntities( term.name ),
							value: term.id,
						};
					} );
					setTermsData( termData );
				}
			} )
			.catch( () => {
				if ( Mounted.current ) {
					setTermsData( [] );
				}
			} );
		return () => {
			Mounted.current = false;
		};
	}, [ taxonomy ] );
	return (
		<SelectControl
			label={ __( 'دسته ها' ) }
			multiple
			onChange={ onChange }
			options={ termsData }
			value={ ChoosedTerms }
		/>
	);
}
