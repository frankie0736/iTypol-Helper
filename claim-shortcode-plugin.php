<?php
/*
Plugin Name: Blog Post Generator基础功能插件
Description: 添加Quiz CPT，设置quiz & post之间的关联（基于MB）；解析[claim], [quiz], [related_quiz_post]短代码，以及相关的css；自动添加quiz schema；修改插件名称。
Version: 1.5
Author: Frankie Xu
*/

if (!defined('ABSPATH')) {
    exit;
}

/* 注册Quiz CPT Start */
add_action( 'init', 'your_prefix_register_post_type' );
function your_prefix_register_post_type() {
	$labels = [
		'name'                     => esc_html__( 'Quizzes', 'your-textdomain' ),
		'singular_name'            => esc_html__( 'Quiz', 'your-textdomain' ),
		'add_new'                  => esc_html__( 'Add New', 'your-textdomain' ),
		'add_new_item'             => esc_html__( 'Add New Quiz', 'your-textdomain' ),
		'edit_item'                => esc_html__( 'Edit Quiz', 'your-textdomain' ),
		'new_item'                 => esc_html__( 'New Quiz', 'your-textdomain' ),
		'view_item'                => esc_html__( 'View Quiz', 'your-textdomain' ),
		'view_items'               => esc_html__( 'View Quizzes', 'your-textdomain' ),
		'search_items'             => esc_html__( 'Search Quizzes', 'your-textdomain' ),
		'not_found'                => esc_html__( 'No quizzes found.', 'your-textdomain' ),
		'not_found_in_trash'       => esc_html__( 'No quizzes found in Trash.', 'your-textdomain' ),
		'parent_item_colon'        => esc_html__( 'Parent Quiz:', 'your-textdomain' ),
		'all_items'                => esc_html__( 'All Quizzes', 'your-textdomain' ),
		'archives'                 => esc_html__( 'Quiz Archives', 'your-textdomain' ),
		'attributes'               => esc_html__( 'Quiz Attributes', 'your-textdomain' ),
		'insert_into_item'         => esc_html__( 'Insert into quiz', 'your-textdomain' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this quiz', 'your-textdomain' ),
		'featured_image'           => esc_html__( 'Featured image', 'your-textdomain' ),
		'set_featured_image'       => esc_html__( 'Set featured image', 'your-textdomain' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image', 'your-textdomain' ),
		'use_featured_image'       => esc_html__( 'Use as featured image', 'your-textdomain' ),
		'menu_name'                => esc_html__( 'Quiz', 'your-textdomain' ),
		'filter_items_list'        => esc_html__( 'Filter quizzes list', 'your-textdomain' ),
		'filter_by_date'           => esc_html__( '', 'your-textdomain' ),
		'items_list_navigation'    => esc_html__( 'Quizzes list navigation', 'your-textdomain' ),
		'items_list'               => esc_html__( 'Quizzes list', 'your-textdomain' ),
		'item_published'           => esc_html__( 'Quiz published.', 'your-textdomain' ),
		'item_published_privately' => esc_html__( 'Quiz published privately.', 'your-textdomain' ),
		'item_reverted_to_draft'   => esc_html__( 'Quiz reverted to draft.', 'your-textdomain' ),
		'item_scheduled'           => esc_html__( 'Quiz scheduled.', 'your-textdomain' ),
		'item_updated'             => esc_html__( 'Quiz updated.', 'your-textdomain' ),
		'text_domain'              => esc_html__( 'your-textdomain', 'your-textdomain' ),
	];
	$args = [
		'label'               => esc_html__( 'Quizzes', 'your-textdomain' ),
		'labels'              => $labels,
		'description'         => '',
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'query_var'           => true,
		'can_export'          => true,
		'delete_with_user'    => true,
		'has_archive'         => true,
		'rest_base'           => '',
		'show_in_menu'        => true,
		'menu_position'       => '',
		'menu_icon'           => 'dashicons-info',
		'capability_type'     => 'post',
		'supports'            => ['title', 'editor'],
		'taxonomies'          => [],
		'rewrite'             => [
			'with_front' => false,
		],
	];

	register_post_type( 'quiz', $args );
}
/* 注册Quiz CPT End */

/* 解析 [claim] Start */

function render_claim_shortcode($atts) {
    $atts = shortcode_atts(
        [
            'claim' => 'No claim provided.',
            'istrue' => 'false',
            'explanation' => 'No explanation available.'
        ],
        $atts,
        'claim'
    );

    $claim = esc_html($atts['claim']);
    $isTrue = filter_var($atts['istrue'], FILTER_VALIDATE_BOOLEAN);
    $explanation = esc_html($atts['explanation']);

    $true_bg_color = get_option('claim_true_bg_color', '#e6f3e6');
    $true_text_color = get_option('claim_true_text_color', '#2e8b57');
    $false_bg_color = get_option('claim_false_bg_color', '#f8e6e6');
    $false_text_color = get_option('claim_false_text_color', '#dc143c');

    $class = $isTrue ? 'claim-true' : 'claim-false';
    $status = $isTrue ? 'True' : 'False';

    $svg_icon = $isTrue 
        ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="transparent" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="transparent" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m14.5 9.5-5 5"/><path d="m9.5 9.5 5 5"/></svg>';

    return "<div class=\"claim {$class}\" style=\"background-color: " . ($isTrue ? $true_bg_color : $false_bg_color) . "; border-color: " . ($isTrue ? $true_bg_color : $false_bg_color) . "; color: " . ($isTrue ? $true_text_color : $false_text_color) . ";\"><p>{$svg_icon} <b>{$claim}</b><span class='claim-true-or-false'>{$status}</span></p><p class='claim-explanation'>{$explanation}</p></div>";
}
add_shortcode('claim', 'render_claim_shortcode');

// 添加设置页面
function claim_shortcode_add_settings_page() {
    add_options_page('Claim Style Settings', 'Claim Style', 'manage_options', 'claim-shortcode-settings', 'claim_shortcode_render_settings_page');
}
add_action('admin_menu', 'claim_shortcode_add_settings_page');

function claim_shortcode_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Claim Style Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('claim_shortcode_settings');
            do_settings_sections('claim-shortcode-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// 注册设置
function claim_shortcode_register_settings() {
    add_settings_section('claim_shortcode_settings_section', 'Color Codes [Default]', null, 'claim-shortcode-settings');

    add_settings_field('claim_true_bg_color', 'True BG Color [#e6f3e6]', 'claim_shortcode_true_bg_color_render', 'claim-shortcode-settings', 'claim_shortcode_settings_section');
    register_setting('claim_shortcode_settings', 'claim_true_bg_color');

    add_settings_field('claim_true_text_color', 'True Text Color [#2e8b57]', 'claim_shortcode_true_text_color_render', 'claim-shortcode-settings', 'claim_shortcode_settings_section');
    register_setting('claim_shortcode_settings', 'claim_true_text_color');

    add_settings_field('claim_false_bg_color', 'False BG Color [#f8e6e6]', 'claim_shortcode_false_bg_color_render', 'claim-shortcode-settings', 'claim_shortcode_settings_section');
    register_setting('claim_shortcode_settings', 'claim_false_bg_color');

    add_settings_field('claim_false_text_color', 'False Text Color [#dc143c]', 'claim_shortcode_false_text_color_render', 'claim-shortcode-settings', 'claim_shortcode_settings_section');
    register_setting('claim_shortcode_settings', 'claim_false_text_color');
}
add_action('admin_init', 'claim_shortcode_register_settings');

// Color Picker 回调函数
function claim_shortcode_true_bg_color_render() {
    $value = get_option('claim_true_bg_color', '#e6f3e6');
    echo '<input type="text" name="claim_true_bg_color" value="' . esc_attr($value) . '" class="color-field" />';
}

function claim_shortcode_true_text_color_render() {
    $value = get_option('claim_true_text_color', '#2e8b57');
    echo '<input type="text" name="claim_true_text_color" value="' . esc_attr($value) . '" class="color-field" />';
}

function claim_shortcode_false_bg_color_render() {
    $value = get_option('claim_false_bg_color', '#f8e6e6');
    echo '<input type="text" name="claim_false_bg_color" value="' . esc_attr($value) . '" class="color-field" />';
}

function claim_shortcode_false_text_color_render() {
    $value = get_option('claim_false_text_color', '#dc143c');
    echo '<input type="text" name="claim_false_text_color" value="' . esc_attr($value) . '" class="color-field" />';
}

// 加载Color Picker的样式和脚本
function claim_shortcode_enqueue_color_picker($hook_suffix) {
    if ($hook_suffix === 'settings_page_claim-shortcode-settings') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('claim_shortcode_color_picker', plugins_url('color-picker.js', __FILE__), ['wp-color-picker'], false, true);
    }
}
add_action('admin_enqueue_scripts', 'claim_shortcode_enqueue_color_picker');
/* 解析 [claim] End */

/* 引入前端 CSS */
function claim_shortcode_enqueue_styles() {
    wp_enqueue_style('claim-shortcode-style', plugins_url('claim-shortcode.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'claim_shortcode_enqueue_styles');
/* 引入前端 CSS */

/* 解析[quiz] Start*/
function quiz_shortcode($atts, $content = null) {
    // 移除自动添加的 <p> 和 <br> 标签
    if ($content) {
        $content = trim($content);
        $content = shortcode_unautop($content);
        $content = preg_replace('/<br\s*\/?>/', '', $content);

        // 解码特殊字符
        $content = wp_specialchars_decode($content, ENT_QUOTES);
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

        // 将智能引号替换为直引号
        $search = array('“', '”', '‘', '’', '‛', '‟', '‹', '›', '«', '»');
        $replace = array('"', '"', "'", "'", "'", '"', "'", "'", '"', '"');
        $content = str_replace($search, $replace, $content);
    }

    // 解析 JSON 数据
    $quiz_data = json_decode($content, true);
    if (!$quiz_data) {
        return '<p>Invalid quiz data. JSON Error: ' . json_last_error_msg() . '</p >';
    }

    // 生成唯一的名称，确保页面上多个测验不会冲突
    $name = 'quiz_' . uniqid();

    // 开始生成 HTML 输出
    $output = '<div class="quiz-container">';
    $output .= '<h2 class="quiz-question">' . esc_html($quiz_data['question']) . '</h2>';

    foreach ($quiz_data['answers'] as $index => $answer) {
        $answer_id = $name . '_answer' . ($index + 1);
        $is_correct = $answer['isCorrect'] ? ' correct-input' : '';
        $output .= '<div class="quiz-option">';
        $output .= '<input type="radio" id="' . esc_attr($answer_id) . '" name="' . esc_attr($name) . '" class="quiz-input' . $is_correct . '">';
        $output .= '<label for="' . esc_attr($answer_id) . '">' . esc_html($answer['answer']) . '</label>';
        $feedback_class = $answer['isCorrect'] ? 'correct' : 'incorrect';
        $output .= '<div class="feedback ' . $feedback_class . '">' . esc_html($answer['hint']) . '</div>';
        $output .= '</div>';
    }

    // 添加切换解释的复选框和标签，仅在正确答案选中后显示
    $toggle_id = $name . '_toggle';
    $output .= '<input type="checkbox" id="' . esc_attr($toggle_id) . '" class="quiz-toggle-input">';
    $output .= '<label for="' . esc_attr($toggle_id) . '" class="quiz-toggle-label">';
    $output .= '<span class="toggle-text"></span>';
    // 添加下拉图标 SVG
    $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>';
    $output .= '</label>';

    // 添加解释部分
    $output .= '<div class="quiz-explanation">' . esc_html($quiz_data['explanation']) . '</div>';

    $output .= '</div>';
    return $output;
}
add_shortcode('quiz', 'quiz_shortcode');
/* 解析 [quiz] End*/

/* 解析 [related_quiz_post] Start*/
function display_related_post_for_quiz() {
    if ( 'quiz' === get_post_type() ) {
        $current_quiz_id = get_the_ID();

        // Correct the direction in get_connected()
        $related_posts = MB_Relationships_API::get_connected( [
            'id' => 'post-to-quiz',
            'to' => $current_quiz_id,
        ] );

        if ( ! empty( $related_posts ) ) {
            // Loop through all related posts if there are multiple
            foreach ( $related_posts as $post ) {
                ?>
                <p class="post-quiz-relationship-link">
                    <span>Quiz by: </span>
                    <a href="<?php echo get_permalink( $post->ID ); ?>" target="_blank" title="Refer to this article for more details"><?php echo get_the_title( $post->ID ); ?></a>
                    <span> — Refer to this article for more details.</span>
                </p >
                <?php
            }
        } else {
            echo '';
        }
    }
}
function related_post_for_quiz_shortcode() {
    ob_start();
    display_related_post_for_quiz();
    return ob_get_clean();
}
add_shortcode( 'related_quiz_post', 'related_post_for_quiz_shortcode' );
/* 解析 [related_quiz_post] End*/

/* relationship Start */

add_action( 'mb_relationships_init', 'post_to_quiz_relationship_custom_func' );

function post_to_quiz_relationship_custom_func() {
    MB_Relationships_API::register( [
        'id'          => 'post-to-quiz',
        'delete_data' => true,
        'reciprocal'  => false,
        'from'        => [
            'object_type'   => 'post',
            'post_type'     => 'post',
            'empty_message' => 'no relationship',
            'admin_filter'  => false,
            'meta_box'      => [
                'closed' => false,
            ],
            'field'         => [
                'add_new' => false,
            ],
        ],
        'to'          => [
            'object_type'   => 'post',
            'post_type'     => 'quiz',
            'empty_message' => 'no meterial relationship',
            'admin_filter'  => false,
            'meta_box'      => [
                'closed' => false,
            ],
            'field'         => [
                'add_new' => false,
            ],
        ],
    ] );
}
/* relationship Start */

/* Automate Generate Quiz Schema Codes on Quiz Singular Pages Start*/
// 在 single quiz 页面加载 schema
function generate_quiz_schema(){  
    if (is_singular('quiz')) {
      
        $content = get_the_content();
    // 使用正则表达式移除 [quiz] 和 [/quiz] 标签，并匹配其中的 JSON 内容
    preg_match_all('/\[quiz\](.*?)\[\/quiz\]/s', $content, $matches);
    
    // 解析提取的 JSON 字符串
    $quizzes = array_map(function($quiz) {
        return json_decode($quiz, true);
    }, $matches[1]);
    
    // 准备生成 schema 结构
    $schema = [
        "@context" => "https://schema.org/",
        "@type" => "Quiz",
        "assesses" => get_the_title(),
        "name" => "Quiz about ".get_the_title(),
        "about" => [
            "@type" => "Thing",
            "name" => get_the_title()
        ],
        "hasPart" => []
    ];
    
    // 转换每个 quiz 数据到 schema 格式
    foreach ($quizzes as $quiz) {
        $question = [
            "@type" => "Question",
            "eduQuestionType" => "Multiple choice",
            "learningResourceType" => "Practice problem",
            "text" => $quiz["question"],
            "comment" => [
                "@type" => "Comment",
                "text" => $quiz["explanation"]
            ],
            "encodingFormat" => "text/markdown",
            "suggestedAnswer" => [],
            "acceptedAnswer" => null
        ];
    
        foreach ($quiz["answers"] as $index => $answer) {
            $answerSchema = [
                "@type" => "Answer",
                "position" => $index,
                "encodingFormat" => "text/markdown",
                "text" => $answer["answer"],
                "comment" => [
                    "@type" => "Comment",
                    "text" => $answer["hint"]
                ]
            ];
    
            if ($answer["isCorrect"]) {
                $question["acceptedAnswer"] = $answerSchema;
            } else {
                $question["suggestedAnswer"][] = $answerSchema;
            }
        }
    
        $schema["hasPart"][] = $question;
    }
    
    // 将生成的 schema 转换为 JSON 并输出
    $quiz_schema = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      echo "<script type='application/ld+json'>$quiz_schema</script>";
    }
    }
    add_action('wp_head', 'generate_quiz_schema');
/* Automate Generate Quiz Schema Codes on Quiz Singular Pages End*/
