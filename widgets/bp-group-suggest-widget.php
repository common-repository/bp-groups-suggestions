<?php
/**
 *last edit 14/12/2022
 *  23/11/2017
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class BP_Group_Suggestion_Widget_Ls extends WP_Widget {

	const TEXTDOMAIN = 'bp-groups-suggestions';

	public function __construct() {
		parent::__construct(
			'bpgrsugls',
			__( 'Group Suggestion Widget', 'bp-groups-suggestions' ),
			array(
				'classname'   => __CLASS__,
				'description' => __( 'Suggest groups for logged in user', 'bp-groups-suggestions' ),
			)
		);
	}

	/**
	 *
	 * @param type $args
	 * @param type $instance
	 * @return type
	 * @version 3, 19/11/2014  $args can also get the following fields
	 * ul_id: the id for the unordered list,
	 * ul_class: the class for the unordered list,
	 * li_class" the class of the list items,
	 *
	 * v2, 6/3/2014, performance enhancement
	 */
	function widget( $args, $instance ) {
		if ( ! is_user_logged_in() ) {
			return; //do not show to non logged in user
		}
		/**
		@since v. 1.8
		*/
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'     => __( 'Group Suggestions', 'bp-groups-suggestions' ),
				'max'       => 5,
				'mikos'     => 0,
				'show_join' => '',
			)
		);

		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$countpossible = BPGroupSuggest::count_possible_groups();
		BPGroupSuggest::suggestions_list(
			$instance['max'],
			$instance['mikos'],
			$instance['show_join'],
			null,
			$args
		);
		if ( $countpossible > $instance['max'] ) :
			?>
				<div  role="navigation"><a href="<?php echo bp_get_groups_slug(); ?>/?scope=lssuggestions" ><?php _e( 'See more suggestions', 'bp-groups-suggestions' ); ?></a></div>
			<?php
	endif;

		echo $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['max']       = absint( $new_instance['max'] );
		$instance['mikos']     = absint( $new_instance['mikos'] );
		$instance['show_join'] = esc_attr( $new_instance['show_join'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'     => __( 'Group Suggestions', 'bp-groups-suggestions' ),
				'max'       => 5,
				'mikos'     => 0,
				'show_join' => '',
			)
		);

		$title     = strip_tags( $instance['title'] );
		$max       = absint( $instance['max'] );
		$mikos     = absint( $instance ['mikos'] );
		$show_join = esc_attr( $instance ['show_join'] );
		?>
		<p>
			<label for="bp-groups-suggest-widget-title"><?php _e( 'Title', 'bp-groups-suggestions' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php echo esc_attr( $title ); ?>" />
			</label>
			</p>
			<p>
			<label for="bp-show-groups-widget-per-page"><?php _e( 'Max Number of suggestions:', 'bp-groups-suggestions' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'max' ); ?>" name="<?php echo $this->get_field_name( 'max' ); ?>" type="text" value="<?php echo esc_attr( $max ); ?>" style="width: 20%" />
			</label>
			</p>
			<p>
			<label for="bp-groups-length"><?php _e( "Number of charachers of group's title:", 'bp-groups-suggestions' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'mikos' ); ?>" name="<?php echo $this->get_field_name( 'mikos' ); ?>" type="text" value="<?php echo esc_attr( $mikos ); ?>" style="width: 20%" /> <br/><small> <?php _e( '0 means that  full group title will be displayed', 'bp-groups-suggestions' ); ?></small>
			</label>
			</p>
			<p>
			<p>
			<label for="<?php echo $this->get_field_id( 'show_join' ); ?>"><?php _e( 'Show join group button?', 'bp-groups-suggestions' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'show_join' ); ?>" name="<?php echo $this->get_field_name( 'show_join' ); ?>" type="checkbox" value="1" <?php checked( '1', $show_join ); ?> />
			</p>
			<?php
	}
}
