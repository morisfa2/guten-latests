import { registerBlockType } from '@wordpress/blocks';
import { Icon, filter } from '@wordpress/icons';
import metadata from './block.json';
import edit from './edit';
import shareData from './ShareData';
// Block registration
registerBlockType( metadata, {
	icon: (
		<Icon icon={ filter } />
	),
	transforms: shareData,
	edit,
} );
