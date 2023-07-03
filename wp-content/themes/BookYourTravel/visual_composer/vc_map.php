<?php
add_action('vc_before_init', 'customLoadVCMapNewLayout');
function customLoadVCMapNewLayout()
{
    vc_map(
        array(
            'name' => 'Home Banner',
            'base' => 'home_banner',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => 'Tilte',
                    'param_name' => 'search_title',
                    'value' => '',
                    'description' => 'This is the searching title of hero section',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Span',
                    'param_name' => 'search_span',
                    'value' => '',
                    'description' => 'This is the searching title of hero section',
                ),
                array(
                    "type" => "attach_image",
                    "heading" => __("Background Image"),
                    "param_name" => "background_image",
                    'description' => 'This is background image of of hero section',
                    "admin_label" => true,
                    "param_holder_class" => "vc_col-sm-6",
                    "edit_field_class" => "vc_column vc_col-sm-6",
                ),

                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
    vc_map(
        array(
            'name' => 'Home Quote',
            'base' => 'home_quote',
            'category' => 'BookYourTravel',
            "params" => [
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('List Member Avatr', 'traveler'),
                    'param_name' => 'list_member',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "attach_image",
                            "heading" => __("Member Avatar", 'traveler'),
                            "param_name" => "member_avatar",
                            "description" => __("Enter your member avatar", 'traveler')
                        ],
                    ),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Quote Content', 'traveler'),
                    'param_name' => 'quote_content',
                    'value' => '',
                    'description' => esc_html__('Enter your Quote', 'traveler'),
                ],
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('List Quote ', 'traveler'),
                    'param_name' => 'list_quote',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "attach_image",
                            "heading" => __("Thumbnail", 'traveler'),
                            "param_name" => "thumbnail_quote_item",
                            "description" => __("Enter Your Thumbnail", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Title", 'traveler'),
                            "param_name" => "title_quote_item",
                            "description" => __("Enter Quote Title.", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Content Quote Item", 'traveler'),
                            "param_name" => "content_quote_item",
                            "description" => __("Enter content quote item", 'traveler')
                        ],
                    ),
                ],
            ]
        )
    );
    vc_map(
        array(
            'name' => 'Home Easy Step',
            'base' => 'easy_step',
            'category' => 'BookYourTravel',
            "params" => [
                [
                    'type' => 'attach_image',
                    'heading' => esc_html__('Background Image', 'traveler'),
                    'param_name' => 'background_image',
                    "description" => __("Upload your background", 'traveler')
                   
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Easy Step Title', 'traveler'),
                    'param_name' => 'easy_title',
                    'value' => '',
                    'description' => esc_html__('Enter your title', 'traveler'),
                ],
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('Step ', 'traveler'),
                    'param_name' => 'step',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "attach_image",
                            "heading" => __("Thumbnail", 'traveler'),
                            "param_name" => "thumbnail_step",
                            "description" => __("Upload Your Thumbnail", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Title", 'traveler'),
                            "param_name" => "step_tile",
                            "description" => __("Enter your title.", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Content", 'traveler'),
                            "param_name" => "step_content",
                            "description" => __("Enter your content", 'traveler')
                        ],
                    ),
                ],
            ]
        )
    );
    vc_map(
        array(
            'name' => 'Home Easy Step',
            'base' => 'easy_step',
            'category' => 'BookYourTravel',
            "params" => [
                [
                    'type' => 'attach_image',
                    'heading' => esc_html__('Background Image', 'traveler'),
                    'param_name' => 'background_image',
                    "description" => __("Upload your background", 'traveler')
                   
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Easy Step Title', 'traveler'),
                    'param_name' => 'easy_title',
                    'value' => '',
                    'description' => esc_html__('Enter your title', 'traveler'),
                ],
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('Step ', 'traveler'),
                    'param_name' => 'step',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "attach_image",
                            "heading" => __("Thumbnail", 'traveler'),
                            "param_name" => "thumbnail_step",
                            "description" => __("Upload Your Thumbnail", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Title", 'traveler'),
                            "param_name" => "step_tile",
                            "description" => __("Enter your title.", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Content", 'traveler'),
                            "param_name" => "step_content",
                            "description" => __("Enter your content", 'traveler')
                        ],
                    ),
                ],
            ]
        )
    );
    vc_map(
        array(
            'name' => 'Location',
            'base' => 'location',
            'category' => 'BookYourTravel',
            "params" => [
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Title', 'traveler'),
                    'param_name' => 'title',
                    'value' => '',
                    'description' => esc_html__('Enter your title', 'traveler'),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Description', 'traveler'),
                    'param_name' => 'description',
                    'value' => '',
                    'description' => esc_html__('Enter your description', 'traveler'),
                ],
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('List Location ', 'traveler'),
                    'param_name' => 'list_location',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "textfield",
                            "heading" => __("ID of the location", 'traveler'),
                            "param_name" => "id",
                            "description" => __("Enter location ID.", 'traveler')
                        ],
                        [
                            "type" => "attach_image",
                            "heading" => __("Background", 'traveler'),
                            "param_name" => "background_img",
                            "description" => __("Upload location background", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Content", 'traveler'),
                            "param_name" => "content",
                            "description" => __("Enter location content", 'traveler')
                        ],
                    ),
                ],
            ]
        )
    );


    vc_map(
        array(
            'name' => 'Reviews',
            'base' => 'reviews',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => 'Tilte',
                    'param_name' => 'title',
                    'description' => 'Enter the reviews title',
                ),
               
                array(
                    "type" => "attach_image",
                    "heading" => __("Background Image"),
                    "param_name" => "background_image",
                    'description' => 'Enter the reviews background',
                ),
                 array(
                    'type' => 'textfield',
                    'heading' => 'Description',
                    'param_name' => 'description',
                    'description' => 'Enter the reviews title',
                ),

                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
}

?>