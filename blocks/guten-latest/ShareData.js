import { createBlock } from '@wordpress/blocks';

const shareData = {
	from: [
		{
			type: 'block',
			blocks: [ 'core/latest-posts' ],
			transform: ( attributes ) => {
				return createBlock( 'morisfa/guten-latest', {
					itemCount: attributes.postsToShow,
					postContent: attributes.displayPostContentRadio,
					imageSize: attributes.featuredImageSizeSlug,
					selectedColor: attributes.selectedColor,
				} );
			},
		},

	],
};

export default shareData;
