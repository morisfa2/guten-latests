<?php
/**
 * The Dynamic Part of Block | latest-guten-block
 * Latest Guten Block.
 *
 * @package latest-guten-block
 */

namespace Morisfa\GutenLatest\Block;

class GutenLatestBlock {
    /**
     * In __construct Run The All Actions
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_block' ) );
        add_action( 'EnqueueBlockEditorAssets', array( $this, 'EnqueueBlockEditorAssets' ) );
        add_action( 'rest_api_init', array( $this, 'RegisterPostsRest' ) );
        add_action( 'rest_api_init', array( $this, 'registerMetaRest' ) );
    }
    /**
     * Register the base of dynamic block with php
     */
    public function register_block() {
        register_block_type_from_metadata(
            dirname( __DIR__ ) . '/blocks/guten-latest/',
            array(
                'render_callback' => array( $this, 'dynamic' ),
            )
        );
    }
    /**
     * One Args Managemant to send attrs and change results as we want - can increase better the project with this way in future
     */
    public function QueryArgs( $attrs ) {

        $Posttype      = array( 'post', 'posts' );
        $Per_page = absint( $attrs['itemCount'] );
        $Arguments = array(
            'post_type'      => $Posttype,
            'posts_per_page' => $Per_page,
            'orderby'        => "date",
            'post_status'    => 'publish',
            'order'          => "desc",
        );

        // Use the new `taxonomies` attribute if available.
        $taxos = ( ! empty( $attrs['taxonomies'] ) ) ? $attrs['taxonomies'] : array();

        if ( ! empty( $taxos ) ) {
            $tax_query = array();

            foreach ( $taxos as $taxonomy ) {
                // Skip this taxonomy if it has no term options.
                if ( empty( $taxonomy['terms'] ) ) {
                    continue;
                }

                $slug = explode( ',', $taxonomy['slug'] );

                $optins = array(
                    'taxonomy' => $slug[0],
                    'field'    => 'id',
                    'terms'    => $taxonomy['terms'],
                );

                if ( isset( $taxonomy['operator'] ) ) {
                    $optins['operator'] = $taxonomy['operator'];
                }

                $tax_query[] = $optins;
            }

            $Arguments['tax_query'] = $tax_query;
        }

        return $Arguments;
    }

    /**
     * The Callback of our base latest guten dynamic block
     * there is many thing manually initialed and can in future easily come from Attrs
     */
    public function dynamic( $attrs ) {
        //register frontend css and js
        wp_enqueue_style( 'guten-user', plugins_url( '/build/guten-user.css', dirname( __FILE__ ) ) );
//        wp_enqueue_style( 'guten-swiper', plugins_url( '/static-assets/swiper-bundle.min.css', dirname( __FILE__ ) ) );
        /**
         * enqueue the js files in footer
         */
//        wp_enqueue_script( 'guten-user-js', plugins_url( '/static-assets/gutenUser.js', dirname( __FILE__ ) ) ,'','',true);
//        wp_enqueue_script( 'guten-user', plugins_url( '/static-assets/swiper-bundle.min.js.js', dirname( __FILE__ ) ),'','',true );





        $initials   = array(
            'customPostType' => 'post,posts',
            'taxonomies'     => array(),
            'itemCount'      => 3,
            'orderBy'        => 'date',
            'postContent'    => 'excerpt',
            'imageSize'      => 'thumbnail',
            'order'          => 'desc',
          
        );
        $attrs = wp_parse_args( $attrs, $initials );
        $Arguments       = $this->QueryArgs( $attrs );
        $posts      = new \WP_Query( $Arguments );
        $selectedColor = $attrs['selectedColor'];


    $blockHtml = '<div class="GutenDesktop" style="background: '.$selectedColor.'">';

        if ( $posts->have_posts() ) {
            while ( $posts->have_posts() ) {
                $posts->the_post();
                $excerpt = get_the_excerpt();
                $excerpt = substr( $excerpt, 0, 100 ); // Only display first 260 characters of excerpt
                $result_excerpt = substr( $excerpt, 0, strrpos( $excerpt, ' ' ) ) . ' ...';


                    $blockHtml .= '<div class="post">';
                    if ( has_post_thumbnail() && 'none' !== $attrs['imageSize'] ) {
                        $blockHtml .= '<div class="post-image">';
                        $blockHtml .= get_the_post_thumbnail( null, $attrs['imageSize'] );
                        $blockHtml .= '</div>';
                    }
                    $blockHtml .= '<a href="'.get_the_permalink().'"><div class="PostTitle">' . get_the_title() . '</div></a>';
                    $blockHtml .= '<div class="PostExcerpt">' . $result_excerpt . '</div>';
                    $blockHtml .= '</div>';
            }
            $blockHtml .= '</div>';

            wp_reset_postdata();
        }

        return $blockHtml;
    }

    /**
     * enqueue our block js
     * NOTIC : this will be enqueued with name guten-latest.js in build and just for ADMIN not all users
     */

    public function EnqueueBlockEditorAssets() {
        wp_enqueue_script(
            'latest-guten-block-editor-script',
            plugins_url( '/blocks/guten-latest/block.js', dirname( __FILE__ ) ),
            array( 'wp-blocks', 'wp-element' ),
            filemtime( plugin_dir_path( __FILE__ ) . '/blocks/guten-latest/block.js' )
        );
    }

    /**
     * register the rest routes to get latest post
     * NOTIC : we have the backend of rest to get data in admin editor front end to show posts changes by categories changes
     * But in the frontend not compeleted the part of code to show this data by changes
     */

    public function RegisterPostsRest() {
        register_rest_route(
            'latest-guten-block/v1',
            '/posts',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'GetLatestData' ),
                'permission_callback' => '__return_true',
            )
        );
    }
    /**
     * register the rest routes to get latest post
     * NOTIC : we have the backend of rest to get data in admin editor front end to show posts changes by categories changes
     * But in the frontend not compeleted the part of code to show this data by changes
     */
    public function registerMetaRest() {
        register_rest_route(
            'latest-guten-block/v1',
            '/meta',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'GetMetaData' ),
                'permission_callback' => '__return_true',
            )
        );
    }
    /**
     * register the rest routes to get latest post
     * NOTIC : we have the backend of rest to get data in admin editor front end to show posts changes by categories changes
     * But in the frontend not compeleted the part of code to show this data by changes
     */
    public function GetLatestData() {
        $Arguments  = array(
            'post_type'      => 'post',
            'orderby'        => 'date',
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'order'          => 'desc',

        );
        $posts = new \WP_Query( $Arguments );
        $posts = array();

        if ( $posts->have_posts() ) {
            while ( $posts->have_posts() ) {
                $posts->the_post();

                $posts[] = array(
                    'id'    => get_the_ID(),
                    'title' => get_the_title(),
                );
            }

            wp_reset_postdata();
        }

        return $posts;
    }
    /**
     * Get List of Categories when clicked on a term in admin editor inspector
     */
    public function GetMetaData() {
        $postTypes = get_post_types( array( 'public' => true ), 'objects' );
        $MetaWpData  = array();

        foreach ( $postTypes as $Posttype ) {
            if ( 'attachment' === $Posttype->name ) {
                continue;
            }

            $MetaWpData[] = array(
                'name'     => $Posttype->name,
                'label'    => $Posttype->label,
                'supports' => $Posttype->supports,
            );
        }

        return $MetaWpData;
    }




}


