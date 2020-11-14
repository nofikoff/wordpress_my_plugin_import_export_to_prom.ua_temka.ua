<?php
/**
 * @package Prom.Ua Exporter
 * @version 1.0
 */
/*
Plugin Name: NOVIKOV модифицированный : Woocomerce ТЮНИНГ +  PROM.UA Exporter (xml)
Plugin URI: https://novikov.ua
Description: Плагин для экспорта каталога товаров на сайт Prom.ua.
Armstrong: My Plugin.
Author: Ruslan Novikov
Version: 9.9999
Author URI: https://novikov.ua
*/


function tatwerat_startSession() {
	if ( ! session_id() ) {
		session_start();
	}
	// кроме этих адресов - фиксируеи гдеп ользовател шарится НУНО для хитрых редиректов потом
	if ( false !== strpos( $_SERVER['REQUEST_URI'], "ajax" ) ) {
		return;
	}
	if ( false !== strpos( $_SERVER['REQUEST_URI'], "json" ) ) {
		return;
	}
	$_SESSION['url_log'][] = $_SERVER['REQUEST_URI'];
}

add_action( 'init', 'tatwerat_startSession', 20 );


// переопределдяем заказ в писме киенту
add_action( 'woocommerce_email_order_details', 'ts_email_order_details', 10, 4 );
function ts_email_order_details( $order, $sent_to_admin, $plain_text, $email ) {

	$mailer = WC()->mailer(); // get the instance of the WC_Emails class
	remove_action( 'woocommerce_email_order_details', array( $mailer, 'order_details' ), 10, 4 );


	//echo '<p>Hey ' . $order->get_billing_first_name() . ', We hope you had fun shopping with us.';
	//$before = '<a class="link" href="' . esc_url($order->get_edit_order_url()) . '">';
	//$after = '</a>';
	echo "<h3>" . wp_kses_post(
			$before .
			sprintf(
				__( 'Order #%s', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)',
				$order->get_order_number(),
				$order->get_date_created()->format( 'c' ),
				wc_format_datetime( $order->get_date_created() )
			)
		) . "</h3>";
	?>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6"
               style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <thead>
            <tr>
                <th class="td" scope="col"
                    style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                <th class="td" scope="col"
                    style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                <th class="td" scope="col"
                    style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			echo wc_get_email_order_items( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$order,
				array(
					'show_sku'      => $sent_to_admin,
					'show_image'    => false,
					'image_size'    => array( 32, 32 ),
					'plain_text'    => $plain_text,
					'sent_to_admin' => $sent_to_admin,
				)
			);
			?>
            </tbody>
            <tfoot>
			<?php
			$item_totals = $order->get_order_item_totals();
			if ( $item_totals ) {
				$i = 0;
				foreach ( $item_totals as $total ) {
					$i ++;
					?>
                    <tr>
                        <th class="td" scope="row" colspan="2"
                            style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
                        <td class="td"
                            style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
                    </tr>
					<?php
				}
			}
			if ( $order->get_customer_note() ) {
				?>
                <tr>
                    <th class="td" scope="row" colspan="2"
                        style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
                    <td class="td"
                        style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                </tr>
				<?php
			}
			?>
            </tfoot>
        </table>
    </div>
	<?php
}

// если это страницв post-new.ph !!!! НЕ ИСПОЛЬЗУЕТСЯ
// меняем по дефолту тип нового продукта
/**if (is_edit_page('new') && "product" == $_REQUEST['post_type']){
 * //yes its an edit page  of a custom post type named Post_Type_Name
 * }**/
function is_edit_page( $new_edit = null ) {
	global $pagenow;
	//make sure we are on the backend
	if ( ! is_admin() ) {
		return false;
	}
	if ( $new_edit == "edit" ) {
		return in_array( $pagenow, array( 'post.php', ) );
	} elseif ( $new_edit == "new" ) //check for new post page
	{
		return in_array( $pagenow, array( 'post-new.php' ) );
	} else //check for either new or edit
	{
		return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}
}

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////Дополнительные кнопки сохранения товара////////////////////////////////////////////////////////
//////////////////////////Дополнительные кнопки сохранения товара////////////////////////////////////////////////////////
//////////////////////////Дополнительные кнопки сохранения товара////////////////////////////////////////////////////////
//////////////////////////Дополнительные кнопки сохранения товара////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//


add_action( 'load-post.php', 'op_register_menu_meta_box' );

function op_render_menu_meta_box() {
	// Metabox content
	echo '<strong>Hi, I am MetaBox.</strong>';
}

/**
 * Calls the class on the post edit screen
 */
function call_someClass() {
	return new someClass();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'call_someClass' );
}

/**
 * The Class
 */
class someClass {
	const LANG = 'some_textdomain';

	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'add_some_meta_box' ) );
	}

	/**
	 * Adds the meta box container
	 */
	public function add_some_meta_box() {
		add_meta_box(
			'some_meta_box_name'
			, __( 'Дополнительные кнопки сохранения товара', self::LANG )
			, array( &$this, 'render_meta_box_content' )
			, 'product'
			, 'advanced'
			, 'high'
		);
	}


	/**
	 * Render Meta Box content
	 */
	public function render_meta_box_content() {
		?>
        <div id="publishing-action" class="mydiv">

            <input name="original_publish" type="hidden" id="original_publish" value="Обновить">

            <input name="save_and_go" type="submit" class="button button-primary button-large" id="publish"
                   value="Обновить и перейти на морду сайта">

            <input name="save" type="submit" class="button button-primary button-large" id="publish"
                   value="Обновить и закрыть">
            <!--                смотри отбработик логики ниже ля этой кнопки-->
            <!--                смотри отбработик логики ниже ля этой кнопки-->
            <!--                смотри отбработик логики ниже ля этой кнопки-->
            <input name="save_refresh" type="submit" class="button button-primary button-large" id="publish"
                   value="Сохранить">


        </div>
		<?php

	}
}

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
/**
 * Use value of post meta for something when the post
 * meta changes
 * после сохранение - закидываем пользователя на два шага назад
 *
 * !!!! ВНИМАНИЕ КОСТЫЛЬ - для простых не вариативных товаров слетел статус  _stock_status
 * во всех формах и пост передается параметр instock а записывется outofstock
 * короче надо попроавить прямо в базе чтобы не ебться !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */

// не сохраняются атрибуты в вариатиыных товарах размеры есл активировать
add_action( 'updated_post_meta', 'saveYouTubeInfo', 10, 4 );

function saveYouTubeInfo( $meta_id, $post_id, $meta_key = '', $meta_value = '' ) {

	//https://stackoverflow.com/questions/20087203/wordpress-hook-after-adding-updating-post-and-after-insertion-of-post-meta

	// иначе - это экшен сохранения

//// Stop if not the correct meta key
//    if ( $meta_key != 'my_meta_field_name') {
//        return false;
//    }

	// костыль - редатирование вариаций создание нового вариативного товара
	// костыль - редатирование вариаций создание нового вариативного товара
	// костыль - редатирование вариаций создание нового вариативного товара
	GLOBAL $wpdb;
	$sql    = "UPDATE `wp_postmeta` set `meta_value` = 'instock' where `meta_key` = '_stock_status'";
	$result = $wpdb->get_results( $sql );

	/**
	 * // костыль какогот о хера цена только регуоярная меняется при апдейте просто прайс не апдейтится ДЛЯ ПРОСТЫХ ТОВАРОВ
	 * // костыль какогот о хера цена только регуоярная меняется при апдейте просто прайс не апдейтится ДЛЯ ПРОСТЫХ ТОВАРОВ
	 * // костыль какогот о хера цена только регуоярная меняется при апдейте просто прайс не апдейтится ДЛЯ ПРОСТЫХ ТОВАРОВ
	 * // костыль какогот о хера цена только регуоярная меняется при апдейте просто прайс не апдейтится ДЛЯ ПРОСТЫХ ТОВАРОВ
	 * // костыль какогот о хера цена только регуоярная меняется при апдейте просто прайс не апдейтится ДЛЯ ПРОСТЫХ ТОВАРОВ
	 *
	 * _regular_price: 109,9
	 * _sale_price: 107
	 **/



	if ( $_POST['post_ID'] AND $_POST['_regular_price'] > 0 ) {
//		$sql    = "UPDATE `wp_postmeta` set `meta_value`= (SELECT meta_value FROM wp_postmeta WHERE `meta_key`= '_regular_price' AND post_id = {$_POST['post_ID']}) where `post_id` = {$_POST['post_ID']} AND meta_key = '_price'";
//		$sql    = "UPDATE `wp_postmeta` set `meta_value`= {$_POST['_sale_price']} where `post_id` = {$_POST['post_ID']} AND meta_key = '_price'";
		$sql    = "UPDATE `wp_postmeta` set `meta_value`= '{$_POST['_regular_price']}' where `post_id` = {$_POST['post_ID']} AND meta_key = '_price'";
		$result = $wpdb->get_results( $sql );
	}


	// если это атсрница редактирования - ничешло нге делаем
	// если это атсрница редактирования - ничешло нге делаем
	// если это атсрница редактирования - ничешло нге делаем
	// если это атсрница редактирования - ничешло нге делаем
	if ( $_GET['action'] == 'edit' ) {
		return;
	}



	if ( $_POST['post_type'] != 'product' ) {
		return;
	}

	// кнопку добавляем выше классс
	// кнопку добавляем выше классс
	// кнопку добавляем выше классс
	if ( isset( $_POST['save_refresh'] ) ) {
		return;
	}


	if ( isset( $_POST['save_and_go'] ) ) {
		wp_redirect( "/?p=" . $_POST['post_ID'] );
		exit;
	}

	// на три шага назад - отк уда пришли в этот товар
	// на три шага назад - отк уда пришли в этот товар
	// на три шага назад - отк уда пришли в этот товар
	$fruit               = array_pop( $_SESSION['url_log'] );
	$fruit               = array_pop( $_SESSION['url_log'] );
	$fruit               = array_pop( $_SESSION['url_log'] );
	$_SESSION['url_log'] = [];
	wp_redirect( $fruit );
	exit;
	/*$cats = wp_get_object_terms( $post_id, 'product_cat' );
	// вторя категория
	if ( isset( $cats[1]->slug ) ) {
		wp_redirect( "/wp-admin/edit.php?product_cat=" . $cats[1]->slug . "&post_type=product" );
		exit;
	} elseif ( isset( $cats[0]->slug ) ) {
		// основная категория
		wp_redirect( "/wp-admin/edit.php?product_cat=" . $cats[0]->slug . "&post_type=product" );
		exit;
	}
*/
}


/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );
function new_loop_shop_per_page( $cols ) {
	// $cols contains the current number of products per page based on the value stored on Options -> Reading
	// Return the number of products you wanna show per page.
	$cols = 24;

	return $cols;
}


/**
 *
 * Наработки
 * 1. Cscart выгрузка в CSV для импорта товаров в WP Импорт / Товары см. http://temka.zt.ua/novikov/2018filterconverter.php
 * 2.http://mamasharit.com/_2018wp_fixdb.php тут пересчет растовок и корретировка вариаций посе импорта в ВП
 * *
 *
 */

// Dinamic price МОДУЛЬ
// отколдичества от категории скилки и пр на каждый товар и пр
// например для диллеров
// https://docs.woocommerce.com/document/woocommerce-dynamic-pricing/

/**
 * тут ВСЕ ФМЛЬТРЫ толковые
 * public_html/wp-content/themes/saleszone/functions/filters.php
 *
 * тут структура Хуков
 * https://businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/
 */
/** ВЫБРАЛ ЭТИ */
//  Ability to add custom attributes to variation select element
// /
//add_filter('woocommerce_dropdown_variation_attribute_options_html', 'saleszone_filter_woocommerce_dropdown_variation_attribute_options_html', 10, 2);
//
//  Woocommerce variations
// /
//add_filter('woocommerce_dropdown_variation_attribute_options_args', 'saleszone_filter_woocommerce_variations_args');
//

// TODO надо чистить весь кэш transients в wp_OPTIONS нахер он нужен см ниже даже Woocommerce wc_delete_product_transients Это делет

// чтобы в админке вегда вариации были разверныте с ценами
//If you're using a child theme you could use:
// get_stylesheet_directory_uri() instead of get_template_directory_uri()
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style() {
	///public_html/wp-content/themes/hestia/admin-style.css
//    wp_register_style( 'admin_css', get_template_directory_uri() . '/admin-style.css', false, '1.0.0' );
//OR
	wp_enqueue_style( 'admin_css', get_template_directory_uri() . '/admin-style.css', false, '1.0.0' );
	///public_html/wp-content/themes/hestia/admin-style.css
	///public_html/wp-content/themes/hestia/admin-style.css
	///public_html/wp-content/themes/hestia/admin-style.css
}


// МЕНЯЕМ СТАНДАРТНУЮ ФУНКЦИЮ WOOCOMMERCE ВЫВОДА ОПЦИЙ - чтобы сразу было "ВЫберите цвет"
// нашел через фразу ВЫБРАТЬ ОПЦИЮ и тупо сюда вставил переопоределил
// оказывается их можно переоопределять
// см ниже ФИЛЬТР вызывается

// ЕСЛИ ВЫБИВАЕТ ОШИБКУ в админке пРИ АКТИВИАЦИИПлагина - переименую
// активируй потом сохрани назад сюда с нуным именем
function wc_dropdown_variation_attribute_options( $args = array() ) {
	//
	$args = wp_parse_args(
		apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
		array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		)
	);

	// Get selected value.
	if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
		$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
		$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
	}

	$options          = $args['options'];
	$product          = $args['product'];
	$attribute        = $args['attribute'];
	$name             = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id               = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class            = $args['class'];
	$show_option_none = (bool) $args['show_option_none'];
//    $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce'); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

	// by Novikov
	$lat_name   = substr( $attribute, 3 );
	$_list_atr_ = wc_get_attribute_taxonomy_labels()[ $lat_name ];


	$show_option_none_text = "* выберите $_list_atr_ * ";


	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
	$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

	if ( ! empty( $options ) ) {
		if ( $product && taxonomy_exists( $attribute ) ) {

			/**
			 * задача отсротировать по этому полю
			 * из таблицы wp_post
			 * menu_order
			 *
			 * т.к. атрибуты рост хранится в иде отдеьлных постов
			 * и по этому полю сортируется в админке
			 * // эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
			 *
			 */

			// Get terms if this is a taxonomy - ordered. We need the names too.
			// эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
//            $terms = wc_get_product_terms(
//                $product->get_id(),
//                $attribute,
//                array(
//                    'fields' => 'all',
//                    'orderby' => 'term_order',
//                )
//            );
// отключим сортировку атрибубуто в  вкарточке товара и оставим не сортировыанные
//The function wc_get_product_terms() uses _wc_get_cached_product_terms() which prioritize stored cached data against the use of WordPress wp_get_post_terms() included in it.
//That's why you can't sort anything.
//So instead you should use directly wp_get_post_terms() which allows sorting like:
			//$terms = wp_get_post_terms(
//            $terms = wp_get_object_terms(


			$terms = wc_get_product_terms(
//            $terms = get_the_terms(

				$product->get_id(),
				$attribute, //pa_razmer и пр
				array(
					/** ВСЯ ЭТА СОРТИРОВКА ПОХУЙ - тут не работает - работает смотри ниже хук */
					/** ВСЯ ЭТА СОРТИРОВКА ПОХУЙ - тут не работает - работает смотри ниже хук */
					/** ВСЯ ЭТА СОРТИРОВКА ПОХУЙ - тут не работает - работает смотри ниже хук */
					// сортируем список атрибутов ка конги в админке указаны порядок
					// сортируем список атрибутов ка конги в админке указаны порядок
//                    'orderby' => 'order', //https://stackoverflow.com/questions/55811525/get-terms-ignores-orderby-argument-on-woocommerce-product-categories
//                    'orderby' => 'none',
					//'order' => 'DESC',
					//'fields'  => 'all',
//                    'fields' => 'ids',
					//'orderby' => 'term_order',
					//'fields' => 'names',
					// эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
					// эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
					// эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
					// эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
					// эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
					// эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара

					//https://codex.wordpress.org/Function_Reference/wp_get_object_terms //orderby
					//(string)
					//name - Default
					//count
					//slug
					//term_group
					//term_order
					//term_id
					//none
				)
			);


			// остальные значения Атрибута
			//НИЖЕ смотри функцуию что вывдит цену для отдельного атритбута в АТРИБУТЕ display_price_in_variation_option_name

			foreach ( $terms as $term ) {

//                print_r($term->taxonomy);
//                print_r($options);
//                exit;
				// КАК ВАРИАНТ - тут написать функцию что вытаскивет цену аттрибута
				// и если это рост = ростьовка динамически ее рассчитывает
				// но как быть с корзиной?


				if ( in_array( $term->slug, $options, true ) ) {
					$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '
                    >' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
				}
			}


//  ВАРИАНТ с  динамическим добавлением доп атрибута РОСТОВКА в карточке товара - не прокатил
//  сложная интеграция с задейстованием JS при котором, если атрибут "чужой" то все вариации на странице блокируются
//            if ($term->taxonomy == 'pa_razmer') {
//                $html .= '<option value="rostovka" class="attached enabled">ростовка</option>';
//            }


		} else {
//            foreach ($options as $option) {
//                // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
//                $selected = sanitize_title($args['selected']) === $args['selected'] ? selected($args['selected'], sanitize_title($option), false) : selected($args['selected'], $option, false);
//                $html .= '<option value="' . esc_attr($option) . '" ' . $selected . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $option, null, $attribute, $product)) . '</option>';
//            }
		}
	}

	$html .= '</select>';
	echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args, 40 ); // WPCS: XSS ok.
}

// ЭТА СОРТИРОВКА РАБОТАЕТ ТОЛЬКО ДЛЯ АДМИНА/ для НЕ зареганых НЕ РАБОТАЕТ !!!!!!!!!!! and is_super_admin() КАКОГО ХУЯ НЕ УБРАЛ
// ЭТА СОРТИРОВКА РАБОТАЕТ ТОЛЬКО ДЛЯ АДМИНА/ для НЕ зареганых НЕ РАБОТАЕТ !!!!!!!!!!! and is_super_admin() КАКОГО ХУЯ НЕ УБРАЛ
// ЭТА СОРТИРОВКА РАБОТАЕТ ТОЛЬКО ДЛЯ АДМИНА/ для НЕ зареганых НЕ РАБОТАЕТ !!!!!!!!!!! and is_super_admin() КАКОГО ХУЯ НЕ УБРАЛ
// ЭТА СОРТИРОВКА РАБОТАЕТ ТОЛЬКО ДЛЯ АДМИНА/ для НЕ зареганых НЕ РАБОТАЕТ !!!!!!!!!!! and is_super_admin() КАКОГО ХУЯ НЕ УБРАЛ
// ЭТА СОРТИРОВКА РАБОТАЕТ ТОЛЬКО ДЛЯ АДМИНА/ для НЕ зареганых НЕ РАБОТАЕТ !!!!!!!!!!! and is_super_admin() КАКОГО ХУЯ НЕ УБРАЛ
function reorder_pa_razmer_options( $array, $number, $taxonomy, $args ) {
	if ( $taxonomy == "pa_razmer" ) {
    //error_log(print_r($args,true));
		global $wpdb;
		$results = $wpdb->get_results( "select min(menu_order) menu_order, meta_value from wp_posts
           join wp_postmeta
           on wp_postmeta.post_id = wp_posts.ID and wp_postmeta.meta_key = 'attribute_pa_razmer'
           where wp_posts.post_parent = " . $number . "
           group by meta_value
           order by min(menu_order) ASC", OBJECT );

//         order by term_order ASC", OBJECT );
//         order by min(menu_order)", OBJECT );
//echo print_r($results,true);
		foreach ( $results as $colororder ) {
//	echo $colororder->meta_value. "<br>";
			foreach ( $array as $key => $color ) {
				if ( $color->slug == $colororder->meta_value ) {
//			echo "Found Match<br>";
					$sortedarray[] = $color;
					unset( $array[ $key ] );
				}
			}
		}
		$array = array_merge( $sortedarray, $array );
	}

	return $array;
}

;

add_filter( 'woocommerce_get_product_terms', 'reorder_pa_razmer_options', 10, 4 );
/////////////////////// ура заработала сортировка !!!!
///  // эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
///  // эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
///  // эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
///  // эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара
///  // эта сортировка допизды также как и сорировка в настройках атрибутов вп ниже смотри лайфхак функия что сорирует размеры по полю term_order - как они расставлены в вариациях товара


/** выводим опшин нейм с ЦЕНОЙ на входе атрибут**/
function display_price_in_variation_option_name( $term ) {
	global $wpdb, $product;

	$result = $wpdb->get_col( "SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'" );

	$term_slug = ( ! empty( $result ) ) ? $result[0] : $term;


	$query = "SELECT postmeta.post_id AS product_id
                FROM {$wpdb->prefix}postmeta AS postmeta
                    LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
                WHERE postmeta.meta_key LIKE 'attribute_%'
                    AND postmeta.meta_value = '$term_slug'
                    AND products.post_parent = $product->id";

	$variation_id = $wpdb->get_col( $query );

	$parent = wp_get_post_parent_id( $variation_id[0] );

	if ( $parent > 0 ) {
		$_product  = new WC_Product_Variation( $variation_id[0] );
		$_currency = get_woocommerce_currency_symbol();
		// срезаем сотые копейки в стринге с точкой
		if ( trim( $_product->get_price() ) ) {
			return $term . ' - ' . number_format( $_product->get_price(), 2, '.', ' ' ) . ' ' . $_currency;
		}

		return '';
	}

	return $term;

}

add_filter( 'woocommerce_variation_option_name', 'display_price_in_variation_option_name' );


/** минимальная цена заказа  */
/** минимальная цена заказа  */
function wc_minimum_order_amount() {
	// Set this variable to specify a minimum order value
	//$minimum = 700;
	$a         = get_option( 'sepw_settings' );
	$minimum   = $a['price_min_order'];
	$flag_acia = 0;

	if ( WC()->cart->total > 0 AND WC()->cart->total < $minimum AND ! $flag_acia ) {
		//if (is_cart()) {
		wc_print_notice(
			sprintf( '<b>Текущая сумма вашего заказа : %s</b> ОПТ от %s грн. Заказы меньше %s грн. не принимается. Удачных и приятных Вам покупок!',
				wc_price( WC()->cart->total ),
				wc_price( $minimum ),
				wc_price( $minimum )
			), 'error'
		);
	}

	// определить есть ли уже скидка - и сообщить
	for ( $i = 6; $i > 0; $i -- ) {
		if ( WC()->cart->total > $a[ 'price_min_acia_00' . $i ] ) {
			wc_print_notice(
				sprintf( '<b>На отобранную сумму товаров %s, вы получите скидку %s%% !</b>',
					wc_price( WC()->cart->total ),
					$a[ 'procent_acia_00' . $i ]
				), 'notice'
			);
			break;
		}
	}

	$flag_acia = false;
	// поощриь к покупке
	for ( $i = 1; $i < 7; $i ++ ) {
		if ( WC()->cart->total < $a[ 'price_min_acia_00' . $i ] ) {
			$delta = $a[ 'price_min_acia_00' . $i ] - WC()->cart->total;
			wc_print_notice(
				sprintf( '<b>Докупите товаров на %s и получите скидку %s%% !</b>',
					wc_price( $delta ),
					$a[ 'procent_acia_00' . $i ]
				), 'success'
			);
			$flag_acia = 1;
			break;
		}
	}


}

function disable_checkout_button() {
	// Set this variable to specify a minimum order value
	$a       = get_option( 'sepw_settings' );
	$minimum = $a['price_min_order'];
	//$total = WC()->cart->get_cart_subtotal();
	$total = WC()->cart->cart_contents_total;
	if ( $total < $minimum ) {
		remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		//echo '<a style="pointer-events: none !important;" href="#" class="checkout-button button alt wc-forward">Proceed to checkout</a>';
	}
}


add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart', 'wc_minimum_order_amount' );
add_action( 'woocommerce_proceed_to_checkout', 'disable_checkout_button', 1 );


//////////////////////////////////не используется//////////////////////////////////////////////////
add_filter( 'woocommerce_product_variation_get_regular_price', 'custom_price', 99, 2 );
add_filter( 'woocommerce_product_variation_get_price', 'custom_price', 99, 2 );
// Variations (of a variable product)
add_filter( 'woocommerce_variation_prices_price', 'custom_variation_price', 99, 3 );
add_filter( 'woocommerce_variation_prices_regular_price', 'custom_variation_price', 99, 3 );
function custom_price( $price, $product ) {
	// Delete product cached price  (if needed)
	wc_delete_product_transients( $product->get_id() );

	return $price; //* 3; // X3 for testing
}

function custom_variation_price( $price, $variation, $product ) {
//    global $sum;
//    $sum += $price;
//    echo " $sum ";
	// https://docs.woocommerce.com/wc-apidocs/class-WC_Product_Variation.html
	// тут подробнее
	//print_r($variation->get_variation_attributes());
	//exit;
	// Delete product cached price  (if needed)
	wc_delete_product_transients( $variation->get_id() );

	return $price;// * 3; // X3 for testing
}


/////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////
//  МЕНЯЕМ грн значек на слово грн
//  МЕНЯЕМ грн значек на слово грн
add_filter( 'woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2 );
function change_existing_currency_symbol( $currency_symbol, $currency ) {
	return ( $currency == 'UAH' ) ? ' грн' : $currency_symbol;
}


// УБИВАЕМ ВКЛАДКУ ДЕТАЛИ карточки товара
// УБИВАЕМ ВКЛАДКУ ДЕТАЛИ карточки товара
// УБИВАЕМ ВКЛАДКУ ДЕТАЛИ карточки товара
add_filter( 'woocommerce_product_tabs', 'bbloomer_remove_product_tabs', 98 );
function bbloomer_remove_product_tabs( $tabs ) {
// если описания нет - вкладку ОПИСАНИ НЕ ВЫВОДИ
// если описания нет - вкладку ОПИСАНИ НЕ ВЫВОДИ
	if ( empty( trim( str_replace( '&nbsp;', ' ', strip_tags( get_the_content() ) ) ) ) ) {
		unset( $tabs['description'] );
	}
// ДЕТАЛИ с списком тпых опций - убираем
	unset( $tabs['additional_information'] );

	return $tabs;
}


/**
 * ////////////////////////////КОММЕНТАРИЙ К ПОЗИЦИИ/////////////////////////////////////////
 * ////////////////////////////КОММЕНТАРИЙ К ПОЗИЦИИ/////////////////////////////////////////
 **/
// ВОТ ЧТО НАДО - по этой стате действуем
// Adding WooCommerce custom fields programmatically
//https://pluginrepublic.com/add-custom-fields-woocommerce-product/
//https://pluginrepublic.com/add-custom-fields-woocommerce-product/
//https://pluginrepublic.com/add-custom-fields-woocommerce-product/
// добавлдяем поле, в карточке товара
//add_action('woocommerce_after_single_variation', 'add_custom_content_for_specific_product', 15);
add_action( 'woocommerce_before_add_to_cart_button', 'add_custom_content_for_specific_product', 15 );
function add_custom_content_for_specific_product() {
	global $product;
	// The content start below (with translatables texts)
	?>
    <div class="custom-content product-id-<?= $product->get_id(); ?>">
        <p><input placeholder="Ваш комментарий к товару" type="text"
                  name="cfwc-title-field">
        </p>
    </div>
	<?php
	// End of content
}

//
// в корзину добавляем значение поля
function cfwc_add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
	/*//[attribute_pa_razmer] => rostovka-3sht
	if (
		!empty($_POST['attribute_pa_razmer'])
		AND
		strpos($_POST['attribute_pa_razmer'], 'rostovka') === 0
	) {
		// новое поле цена ростовки
		$cart_item_data['rostovka'] = '11111';
		print_r($_POST);
		exit;
	}*/


	if ( ! empty( $_POST['cfwc-title-field'] ) ) {
		$cart_item_data['title_field'] = $_POST['cfwc-title-field'];
	}

	return $cart_item_data;
}

add_filter( 'woocommerce_add_cart_item_data', 'cfwc_add_custom_field_item_data', 10, 4 );
//
// выводим комменты всписке товаров корзины
function cfwc_cart_item_name( $name, $cart_item, $cart_item_key ) {

	if ( isset( $cart_item['title_field'] ) ) {
		$name .= sprintf(
			'<p>Ваш комментарий: %s</p>',
			esc_html( $cart_item['title_field'] )
		);
	}

	return $name;
}

add_filter( 'woocommerce_cart_item_name', 'cfwc_cart_item_name', 10, 3 );
//
//  выводим комменты в заказе
function cfwc_add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
	foreach ( $item as $cart_item_key => $values ) {
		if ( isset( $values['title_field'] ) ) {
			$item->add_meta_data( 'Комментарий к позиции', $values['title_field'], true );
		}
	}
}

add_action( 'woocommerce_checkout_create_order_line_item', 'cfwc_add_custom_data_to_order', 10, 4 );
/**
 * ////////////////////////////THE END - КОММЕНТАРИЙ К ПОЗИЦИИ/////////////////////////////////////////
 **/


// ПОКАЗЫВАТЬ ТОКА МИН цену
function shop_variable_product_price( $price, $product ) {
	$variation_min_reg_price  = $product->get_variation_regular_price( 'min', true );
	$variation_min_sale_price = $product->get_variation_sale_price( 'min', true );
	if ( $product->is_on_sale() && ! empty( $variation_min_sale_price ) ) {
		if ( ! empty( $variation_min_sale_price ) ) {
			$price = '<del class="strike">' . woocommerce_price( $variation_min_reg_price ) . '</del>
        <ins class="highlight">' . woocommerce_price( $variation_min_sale_price ) . '</ins>';
		}
	} else {
		if ( ! empty( $variation_min_reg_price ) ) {
			$price = '<ins class="highlight">' . woocommerce_price( $variation_min_reg_price ) . '</ins>';
		} else {
			$price = '<ins class="highlight">' . woocommerce_price( $product->regular_price ) . '</ins>';
		}
	}

	return $price;
}

add_filter( 'woocommerce_variable_sale_price_html', 'shop_variable_product_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'shop_variable_product_price', 10, 2 );


// ИЗ ПОЛОЖИТЕЛЬНОГО
// - Каждому товару привязан свой Артикул
// - Цена и наличие всех товаров из магазина будет обновляться автоматом
// - Вновь залитые товары проставил наличие ОТСУСТСВУЮТ, пока не донаполним
// - Обновление цен и наличия товара будет автоматом
//
// ИЗ ОТРИЦАТЕЛЬНОГО, ЭТО Первичный грязный импорт
// - Картинки только одна, остальные руками добавлять
// - Характеристики залились но как то странно https://temka-opt.prom.ua/p950317778-bodi-nakat-prikoly.html - написал в службу поддержки жду
// - Также не залился производитель ТМ Темка
// - В ручную необходимо проставить ключевые слова, описывающие товар
// Variable
/////////////////////////////////////////////////////////////////////////////////
add_action( 'admin_menu', 'xml_exporter_panel' );
function xml_exporter_panel() {

	add_submenu_page(
		'woocommerce',
		'Simple Excel Pricelist for WooCommerce',
		'РОСТОВКИ + Prom.ua',
		'manage_options',
		'xml-exporter-panel',
		array( 'create_admin_page' )
	);

// ЭТО НЕ УДАЛЯЙ без него не раотает ссылак выше в подрубрике WOOCOMMERCE
// ЭТО НЕ УДАЛЯЙ без него не раотает ссылак выше в подрубрике WOOCOMMERCE
// ЭТО НЕ УДАЛЯЙ без него не раотает ссылак выше в подрубрике WOOCOMMERCE
	add_menu_page(
		'Экспорт товаров',
		'Экспорт товаров',
		'manage_options',
		'xml-exporter-panel',
		'xml_exporter_page',
		'/wp-content/plugins/novikov_promua_exporter/assets/img/icon.ico' );
	//
}

function xml_exporter_page() {
	$path = dirname( __FILE__ );
	$name = 'export.xml';
	echo '<div class="wrap"><br><div> <h3>Экспорт товаров для загрузки на сайт &mdash; <img style=" vertical-align:middle;" src="../wp-content/plugins/novikov_promua_exporter/assets/img/logo_main-trans.png" /></h3></div><br />';

	//
	if ( $_POST['is_export_with_images'] == "yes" ) {
		xml_generate( false );
	}
	//
	if ( $_POST['is_export_no_images'] == "yes" ) {
		xml_generate( true );
	}
	//
	if ( $_POST['is_refresh_rostovka'] == "yes" ) {
		refresh_rostovka();
	}

	echo '
                <br />
                <form action="" method="POST">
                <input type="hidden" value="yes" name="is_export_no_images"/>
                <input type="submit" value="Выгрузить XML файл БЕЗ КАРТИНОК" class="button button-primary" style="width: 300px; height: 35px"/>
                Такая выгрузка быстрее зальется на Prom.ua т.к. тому не придется сливать каждую картинку
                </form>
                
                <br />
                <form action="" method="POST">
                <input type="hidden" value="yes" name="is_export_with_images"/>
                <input type="submit" value="Полный XML файл с картинками" class="button button-secondary" style="width: 300px; height: 35px"/>
                Экспорт на  Prom.ua может занять 10-20 минут
                </form>
                <br />
                Рездультирующий файл всегда по одному и тму же адресу обновляется 
                <a href="/wp-content/plugins/novikov_promua_exporter/export.xml">export.xml</a>
                
                <br />
                <br />
                <hr />
                <br />
                <form action="" method="POST">
                <input type="hidden" value="yes" name="is_refresh_rostovka"/>
                <input type="submit" value="Обновить ростовки в магазине" class="button button-secondary" style="width: 300px; height: 35px"/>
                Перерасчет всех Ростовок с ценами и штуками
                </form>
                <br />
                 
                ';


}


// ВАЖНО - имя ростовки или slug не менять - ибо это справочная информацяи одновременно в разных тоарах используется
// МЕНЯЕМ ТОКА ЦЕНУ
// !!!! БЛЯ а надо бы и ростоку добавить где ее нет ибо заказчик ленив - тока рамеры добавил и ждет ростовку
// ростовку считает корректно !
function refresh_rostovka() {

	// это вариационные подтиовары для 8684 - product_variation
	//$args = array('post__in' => [8696, 8697, 8698], 'post_type' => 'product_variation', 'posts_per_page' => -1, 'product_cat' => 0, 'orderby' => 'name');
	//$args = array('post__in' => [4190, 8684, 4899], 'post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => 0, 'orderby' => 'name');
	//$args = array('post__in' => [16902], 'post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => 0, 'orderby' => 'name');
	//   $args = array('post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => 0, 'orderby' => 'name');


	$args = array( 'post_type' => 'product', 'posts_per_page' => - 1, 'orderby' => 'name' );
//    $args = array('post__in' => [24786], 'post_type' => 'product', 'posts_per_page' => -1, 'orderby' => 'name');
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		setup_postdata( $post );
		$_post_id = $post->ID;


		// какие атрибуты в посту
		$stock_status = get_post_meta( $_post_id, '_product_attributes' );
		// перебираем каждый и твыасикваем значения
		$rostovka_summa = 0;
		$product        = new WC_Product_Variable( $_post_id );


		$variations     = $product->get_available_variations();
		$rostovka_summa = 0;
		// количество ростов в этой позиции - если  ти ростовку не добавляаем и не персчитываем
		$number_sizes = 0;
		// ростовки нет
		$flag_rostovka_ye = 0;
		//переберем все атрибуты типа рост pa_razmer
		foreach ( $variations as $variation ) {
			//ростовку пропускаем
			if ( $variation['attributes']['attribute_pa_razmer'] == 'rostovka' ) {
				$flag_rostovka_ye = 1;
				continue;
			}
			//
			$rostovka_summa = $rostovka_summa + $variation['display_price'];
			//
			if ( isset( $variation['attributes']['attribute_pa_razmer'] ) ) {
				$number_sizes ++;
			}
		};
		// ЕСЛИ сумма всех ростов нудевая то Ротовку не трогаем не обновляеми не создем
		if ( $rostovka_summa == 0 OR $number_sizes < 2 ) {
			echo "\n<br>У этого товара нет или один размер, пропускаем   <a href='https://temka.zt.ua/wp-admin/post.php?post=$post->ID&action=edit'>ID $post->ID</a>";
			continue;
		}
		//////////////////////////////// АВТОМАТ ДОБАВЛЕНИЯ РОСТОВОК - не понадобился т.к. зависит от набора размеров //////////////////////////////
		/*        if (!$flag_rostovka_ye AND $number_sizes > 1) {
					// если Ростовки нет - это новый толвар - добавяем
					$variation_data = array(
						'attributes' => array(
							'razmer' => 'rostovka',
						),
						'sku' => '',
						'regular_price' => '1.00',
						'sale_price' => '',
						'stock_qty' => 999,
					);
					create_product_variation($post->ID, $variation_data);
					////////////////// конец добваления ростовки
					echo "\n<br>добавлена ростовка для  <a href='https://temka.zt.ua/wp-admin/post.php?post=$post->ID&action=edit'>ID $post->ID</a>";
				}
		*/


		/////////////////////////////////////////// переасчет ростовок ///////////////////////////////////////////////////////////
		$slug_rostovka_latin = '';
		// ОПРЕДЖЕЛЯЕМ Есть ли ростовка и ее цену
		foreach ( $stock_status[0] as $_key_name_atribute_latin => $item ) {
			if ( $_key_name_atribute_latin == 'pa_razmer' ) {
				$_post_pretag = get_the_terms( $_post_id, 'pa_razmer' );
			}
			foreach ( $_post_pretag as $_item ) {
				if ( preg_match( '/ростов/ui', $_item->name ) ) {
					$slug_rostovka_latin = $_item->slug;
					echo " -> инфа для отладки пото убрать ... <a href='https://temka.zt.ua/wp-admin/post.php?post=$post->ID&action=edit'>ID $post->ID</a> - $slug_rostovka_latin, ";
				} else {
					// сумма стоимости ростовок
					//$rostovka_summa=$rostovka_summa+$_item->
				}
//                print_r($_item);
//                exit;
			}
		}
		if ( $slug_rostovka_latin != '' ) {
			$args      = array(
				'post_parent'    => $post->ID,
				'posts_per_page' => - 1,
				'post_type'      => 'product_variation', //you can use also 'any'
			);
			$sub_posts = get_posts( $args );
			foreach ( $sub_posts as $sub_post ) {
				setup_postdata( $sub_post );
				$stock_status = get_post_meta( $sub_post->ID, 'attribute_pa_razmer' );
				//в этом дочернем товаре вариации - есть такая Slug ??
				if ( $stock_status[0] == $slug_rostovka_latin ) {
					echo "\n<br>апдейтим ростовку сабпост ID $sub_post->ID (не путать <a href='https://temka.zt.ua/wp-admin/post.php?post=$post->ID&action=edit'>ID $post->ID</a> основного товаром)";
					//meta_key in postmeta _price _regular_price
					update_post_meta( $sub_post->ID, '_price', $rostovka_summa );
					update_post_meta( $sub_post->ID, '_regular_price', $rostovka_summa );
				}
				// перебираем каждый и твыасикваем значения
				//print_r($stock_status);
			}

			//UPDATE `wp_terms` SET `name` = 'ростовка 2шт' WHERE `term_id` = 81;
		}
	}

}


function xml_generate( $no_img_faster ) {
	$path = dirname( __FILE__ );
	$name = 'export.xml';


	get_all_categories();
	$xml = new SimpleXMLElement( '<?xml version="1.0" encoding="utf-8"?><price></price>' );
	$xml->addAttribute( 'date', date( 'Y-m-d H:i:s' ) );
	$catalog = $xml->addChild( 'catalog' );

	$__countOfCats = count( $GLOBALS['cats_array'] );
	$i             = 0;
	while ( $i < $__countOfCats ) {
		$category = $catalog->addChild( 'category', $GLOBALS['cats_array'][ $i ]['cat_name'] );
		$category->addAttribute( 'id', $GLOBALS['cats_array'][ $i ]['category_id'] );
		if ( $GLOBALS['cats_array'][ $i ]['parent_id'] != 0 ) {
			$category->addAttribute( 'parentID', $GLOBALS['cats_array'][ $i ]['parent_id'] );
		}
		$i ++;
	}

	// НЕ ПУТАЙ ID внутренний с SKU ! на PROMUA виден тока SKU
	// по факту SKU у нас это ID старого движка на CSCART
	// соответсвеи смотри в файле выгрузки поля ID и SKU
	//4899
//            $args = array('post__in' => [4190, 8684, 4899], 'post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => 0, 'orderby' => 'name');
	$args  = array( 'post_type' => 'product', 'posts_per_page' => - 1, 'product_cat' => 0, 'orderby' => 'name' );
	$posts = get_posts( $args );
	$items = $xml->addChild( 'items' );
	foreach ( $posts as $post ) : setup_postdata( $post );


		$_post_id    = $post->ID;
		$_post_title = strip_all_for_xml( $post->post_title );

		// NAME поле обязательное
		if ( trim( $_post_title ) == '' ) {
			echo "<h1>НУЛЕВОЕ НАЗВАНИЕ пропускаем</h1>\r\n";
			print_r( $post );
			continue;
		}

		$_post_desc    = strip_all_for_xml( $post->post_content );
		$_post_link    = $post->post_name;
		$_post_preterm = get_the_terms( $_post_id, 'product_cat' );
		foreach ( $_post_preterm as $term ) {
			//if($term->object_id == $_post_id) {
			$_post_term = $term->term_id;
			//}
//                    $_post_pretag = get_the_terms($_post_id, 'product_tag');
//                    foreach ($_post_pretag as $tag) {
//                        if ($tag->object_id == $_post_id) {
//                            $_post_vendor = $tag->name;
//                        }
//                    }
		}


		$atributes = get_ciliricca_named_options_attributes( $_post_id );


		$price = get_post_meta( $_post_id, '_regular_price' );
		$price = $price[0];
		// ЦЕНЫ НЕТ - бедем из вариативных пока первую попавшуюся
		// TODO: брать минимальную из вариаций??
		if ( ! $price OR $price == '0.000' ) {
			$price = get_post_meta( $_post_id, '_price' );
			$price = $price[0];
			if ( $price == '0.000' ) {
				echo "<h1>НУЛЕВАЯ ЦЕНА пропускаем</h1>\r\n";
				print_r( $post );
				continue;
			}
		}


		$sku          = get_post_meta( $_post_id, '_sku' );
		$sku          = $sku[0]; // vendor code
		$stock_status = get_post_meta( $_post_id, '_stock_status' );
		$stock_status = $stock_status[0]; // stock_status
		if ( $stock_status == "instock" ) {
			$stock_status = "true";
		} else {
			if ( ! empty( $stock_status ) ) {
				$stock_status = "false";
			}
		}

		$images = &get_children( array(
			'post_parent'    => $_post_id,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image'
		) );

		$_post_images = '';
		if ( empty( $images ) ) {
		} else {
			foreach ( $images as $attachment_id => $attachment ) {
				//$_post_images .= ' ' . wp_get_attachment_url($attachment_id, 'large');
				// XML поддерживает тока одну картинку !! берем последнюю
				$_post_images = wp_get_attachment_url( $attachment_id, 'large' );
			}
			$_post_images = trim( $_post_images );
		}

		// ФОРМАТ ДОКУМЕНТА http://support.prom.ua/documents/844
		// ФОРМАТ ДОКУМЕНТА http://support.prom.ua/documents/844
		// ФОРМАТ ДОКУМЕНТА http://support.prom.ua/documents/844
		//
		$item = $items->addChild( 'item' );
		$item->addAttribute( 'id', $_post_id );
		$item->addChild( 'name', $_post_title );
		$item->addChild( 'categoryId', $_post_term );
		// по поводу цены http://support.prom.ua/documents/844
		$item->addChild( 'price', $price );
		$item->addChild( 'url', get_permalink( woocommerce_get_page_id( 'shop' ) ) . $_post_link );
		if ( ! $no_img_faster ) {
			$item->addChild( 'image', $_post_images );
		}
		$item->addChild( 'vendor', 'ТМ Тёмка' );
		$item->addChild( 'country', 'Украина' );
		$item->addChild( 'vendorCode', $sku );
		$item->addChild( 'description', $_post_desc );
		//$item->addChild('available', $stock_status);
		$item->addChild( 'available', '' ); // все новые позиции НЕТ В НАЛИЧИИ!
		//
		foreach ( $atributes as $_name_a => $_values_a ) {
			//<param name="Название_характеристики"> Значение_характеристики_товара</param>
			$action = $item->addChild( 'param', implode( "|", $_values_a ) );
			$action->addAttribute( 'name', $_name_a );
		}

	endforeach;

	//
	$dom                     = new DOMDocument( '1.0' );
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput       = true;
	$dom->loadXML( $xml->asXML() );
	$dom->save( $path . '/' . $name );

	/*
				//
				if (file_exists($path . '/' . $name)) {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=' . basename($path . '/' . $name));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($path . '/' . $name));
					ob_clean();
					flush();
					readfile($path . '/' . $name);
					exit;
				}
	*/
	wp_reset_query();


}


function download_xml_file() {
	$path = dirname( __FILE__ );
	$name = 'export.xml';


	if ( file_exists( $path . '/' . $name ) ) {
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . basename( $path . '/' . $name ) );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $path . '/' . $name ) );
		ob_clean();
		flush();
		readfile( $path . '/' . $name );
		exit;
	} else {
		echo "Ошибка! Файла пока нет, сначала его нужно создать, воспользуйтесь второй кнопкой!";
	}
}

function get_all_categories( $parent_id__ = 0 ) {

	$taxonomy     = 'product_cat';
	$orderby      = 'name';
	$show_count   = 0;
	$pad_counts   = 0;
	$hierarchical = 1;
	$title        = '';
	$empty        = 0;

	$args = array(
		'taxonomy'     => $taxonomy,
		'child_of'     => 0,
		'parent'       => $parent_id__,
		'orderby'      => $orderby,
		'show_count'   => $show_count,
		'pad_counts'   => $pad_counts,
		'hierarchical' => $hierarchical,
		'title_li'     => $title,
		'hide_empty'   => $empty
	);

	$sub_cats = get_categories( $args );
	if ( $sub_cats ) {
		foreach ( $sub_cats as $sub_category ) {
			if ( $sub_cats->$sub_category == 0 ) {
				$GLOBALS['cats_array'][] = array(
					'category_id' => $sub_category->term_id,
					'parent_id'   => $parent_id__,
					'cat_name'    => strip_all_for_xml( $sub_category->name )
				);
				get_all_categories( $sub_category->term_id );
			}
		}
	}
}

function strip_all_for_xml( $str ) {
	$str = strip_tags( $str );
	$iso = array(
		"&nbsp;" => " ",
		"&"      => "и",
		"\r"     => " ",
		"\n"     => " ",
	);
	$str = strtr( $str, $iso );
	// режем двойные пробелы
	$str = preg_replace( '/[\t\n\r\0\x0B]/', '', $str );
	$str = preg_replace( '/([\s])\1+/', ' ', $str );
	$str = trim( $str, '. ' );

	return $str;
}


function get_ciliricca_named_options_attributes( $_post_id ) {
	/**
	 * ПОЛУЧАЕМ АССОЦИАТИВНЫЙ МАССИВ ВСЕХ АТРЕБЬУТОВ НА КИРИЛИЦЕ $atributes
	 */
	// справочник имен латиница - кририлица  параметров вариаацйи атритбьутов
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
			$attribute_taxonomies_arr[ 'pa_' . $attribute_taxonomy->attribute_name ] = $attribute_taxonomy->attribute_label;
		}
	}
	$atributes = [];
// какие атрибуты в посту
	$stock_status = get_post_meta( $_post_id, '_product_attributes' );
// перебираем каждый и твыасикваем значения
	foreach ( $stock_status[0] as $_key_name_atribute_latin => $item ) {
		$_post_pretag = get_the_terms( $_post_id, $_key_name_atribute_latin );
		foreach ( $_post_pretag as $_item ) {
			$atributes[ $attribute_taxonomies_arr[ $_key_name_atribute_latin ] ][] = $_item->name;
		}
	}

	/**
	 *  END $atributes
	 *
	 */
	return $atributes;

}

/**
 * Create a product variation for a defined variable product ID.
 *
 * @param int $product_id | Post ID of the product parent variable product.
 * @param array $variation_data | The data to insert in the product.
 *
 * @since 3.0.0
 */
// источник https://stackoverflow.com/questions/47518280/create-programmatically-a-woocommerce-product-variation-with-new-attribute-value

function create_product_variation( $product_id, $variation_data ) {
	// Get the Variable product object (parent)
	$product = wc_get_product( $product_id );

	$variation_post = array(
		'post_title'  => $product->get_title(),
		'post_name'   => 'product-' . $product_id . '-variation',
		'post_status' => 'publish',
		'post_parent' => $product_id,
		'post_type'   => 'product_variation',
		'guid'        => $product->get_permalink()
	);

	// Creating the product variation
	$variation_id = wp_insert_post( $variation_post );

	// Get an instance of the WC_Product_Variation object
	$variation = new WC_Product_Variation( $variation_id );

	// Iterating through the variations attributes
	foreach ( $variation_data['attributes'] as $attribute => $term_name ) {
		$taxonomy = 'pa_' . $attribute; // The attribute taxonomy
		//attribute_pa_razmer

		// If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
		if ( ! taxonomy_exists( $taxonomy ) ) {
			register_taxonomy(
				$taxonomy,
				'product_variation',
				array(
					'hierarchical' => false,
					'label'        => ucfirst( $attribute ),
					'query_var'    => true,
					'rewrite'      => array( 'slug' => sanitize_title( $attribute ) ), // The base slug
				)
			);
		}

		// Check if the Term name exist and if not we create it.
		if ( ! term_exists( $term_name, $taxonomy ) ) {
			wp_insert_term( $term_name, $taxonomy );
		} // Create the term

		$term_slug = get_term_by( 'name', $term_name, $taxonomy )->slug; // Get the term slug
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг
		// !!!!!!!!!!!!!!!!! не стал разбираться - тут сербезный косяк не может достать РОСТОВКУ слуг

		$term_slug = 'rostovka'; // Get the term slug

		// Get the post Terms names from the parent variable product.
		//$post_term_names = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'names'));
		$post_term_names = wc_get_product_terms( $product_id, $taxonomy, array( 'fields' => 'names' ) );

		// Check if the post term exist and if not we set it in the parent variable product.
		if ( ! in_array( $term_name, $post_term_names ) ) {
			wp_set_post_terms( $product_id, $term_name, $taxonomy, true );
		}

		// Set/save the attribute data in the product variation
		update_post_meta( $variation_id, 'attribute_' . $taxonomy, $term_slug );


	}

	## Set/save all other data

	// SKU
	if ( ! empty( $variation_data['sku'] ) ) {
		$variation->set_sku( $variation_data['sku'] );
	}

	// Prices
	if ( empty( $variation_data['sale_price'] ) ) {
		$variation->set_price( $variation_data['regular_price'] );
	} else {
		$variation->set_price( $variation_data['sale_price'] );
		$variation->set_sale_price( $variation_data['sale_price'] );
	}
	$variation->set_regular_price( $variation_data['regular_price'] );

	// Stock
	if ( ! empty( $variation_data['stock_qty'] ) ) {
		$variation->set_stock_quantity( $variation_data['stock_qty'] );
		$variation->set_manage_stock( true );
		$variation->set_stock_status( '' );
	} else {
		$variation->set_manage_stock( false );
	}

	$variation->set_weight( '' ); // weight (reseting)

	$variation->save(); // Save the data
}


////показщываем на главно странице магазина категорию только определенной категороии по слагу
add_action( 'woocommerce_product_query', 'bbloomer_hide_products_category_shop' );
function bbloomer_hide_products_category_shop( $q ) {


	if ( is_front_page() && is_home() ) {
		// Default homepage
	} elseif ( is_front_page() ) {
		// Static homepage
		$tax_query   = (array) $q->get( 'tax_query' );
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( 'novinki' ), // Category slug here
			// исключеам 'operator' => 'NOT IN'
		);
		$q->set( 'tax_query', $tax_query );

	} elseif ( is_home() ) {
		// Blog page
	}
}


// если в карточке товара надо вывестик акуюто инфу из поста или страницы
// info добавь код ниже в шаблона
//   wp-content/themes/hestia/woocommerce/content-single-product.php
//    wc_print_notice(
//        trim(apply_filters(
//            'the_content',
//            get_post_field('post_content', 16981)
//
//        ), "<p\/> \n"),
//        'notice'
//    );


//add_action( 'woocommerce_before_add_to_cart_button', 'content_before_addtocart_button' );
//
//function content_before_addtocart_button() {
//	global $post;
//
//	$terms = get_the_terms( $post->ID, 'product_cat' );
//
//	foreach ($terms as $term) {
//		if( $term->slug === 'jackets')
//			echo '<div class="content-section">add custom size <a href="' . esc_url( get_term_link( $term->term_id, 'product_cat' ) ) . '">' . $term->name . '</a></div>';
//	}
//}
