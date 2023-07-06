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
                    'type' => 'checkbox',
                    'heading' => 'Disable Search Field',
                    'param_name' => 'disable_search',
                    'value' => '',
                    'description' => 'Disable search input',
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

    vc_map(
        array(
            'name' => 'Introduction',
            'base' => 'introduction',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => 'Tilte',
                    'param_name' => 'title',
                    'description' => 'Enter the introduction title',
                ),

                array(
                    "type" => "attach_image",
                    "heading" => __("Avatar Image"),
                    "param_name" => "avatar_image",
                    'description' => 'Enter the avatar image',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Description',
                    'param_name' => 'description',
                    'description' => 'Enter the introduction title',
                ),

                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
    vc_map(
        array(
            'name' => 'Certification',
            'base' => 'certification',
            'category' => 'BookYourTravel',
            "params" => [
                [
                    'type' => 'attach_image',
                    'heading' => esc_html__('Logo', 'traveler'),
                    'param_name' => 'logo',
                    'value' => '',
                    'description' => esc_html__('Enter your logo', 'traveler'),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Description', 'traveler'),
                    'param_name' => 'description',
                    'value' => '',
                    'description' => esc_html__('Enter your description', 'traveler'),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Content', 'traveler'),
                    'param_name' => 'span',
                    'value' => '',
                    'description' => esc_html__('Enter your Content', 'traveler'),
                ],
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('List certification image ', 'traveler'),
                    'param_name' => 'list_certification_image',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "textfield",
                            "heading" => __("Link", 'traveler'),
                            "param_name" => "link",
                            "description" => __("Enter location ID.", 'traveler')
                        ],
                        [
                            "type" => "attach_image",
                            "heading" => __("image", 'traveler'),
                            "param_name" => "image",
                            "description" => __("Enter location ID.", 'traveler')
                        ],
                    ),
                ],
                [
                    'type' => 'attach_image',
                    'heading' => esc_html__('Brand Image', 'traveler'),
                    'param_name' => 'brand_image',
                    'value' => '',
                    'description' => esc_html__('Enter your Brand Image', 'traveler'),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Brand Rating Title', 'traveler'),
                    'param_name' => 'rating_title',
                    'value' => '',
                    'description' => esc_html__('Enter your Brand Image', 'traveler'),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Brand Rating Number', 'traveler'),
                    'param_name' => 'rating_number',
                    'value' => '',
                    'description' => esc_html__('Enter your Brand Rating Number', 'traveler'),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Brand Ranking Title', 'traveler'),
                    'param_name' => 'ranking_title',
                    'value' => '',
                    'description' => esc_html__('Enter your Brand Ranking Title', 'traveler'),
                ],
                [
                    'type' => 'textfield',
                    'heading' => esc_html__('Brand Ranking', 'traveler'),
                    'param_name' => 'ranking',
                    'value' => '',
                    'description' => esc_html__('Enter your Brand Ranking', 'traveler'),
                ],
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('List traveler reivew ', 'traveler'),
                    'param_name' => 'list_traveler_review',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "textfield",
                            "heading" => __("Traveler reviews", 'traveler'),
                            "param_name" => "traveler_reviews",
                            "description" => __("Enter Traveler reviews.", 'traveler')
                        ],
                    ),
                ],
            ]
        )
    );
    vc_map(
        array(
            'name' => 'Hotline Information',
            'base' => 'hotline_information',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => 'Name',
                    'param_name' => 'name',
                    'description' => 'Enter name of the person who owns the phone number',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Phone Number',
                    'param_name' => 'phone_number',
                    'description' => 'Enter the phone number',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Email',
                    'param_name' => 'email',
                    'description' => 'Enter the email',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Working time',
                    'param_name' => 'working_time',
                    'description' => 'Enter the working time',
                ),

                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
    vc_map(
        array(
            'name' => 'About us',
            'base' => 'about_us',
            'category' => 'BookYourTravel',
            "params" => [
                [
                    'type' => 'param_group',
                    'heading' => esc_html__('About us content ', 'traveler'),
                    'param_name' => 'about_us_content',
                    'value' => '',
                    'params' => array(
                        [
                            "type" => "textfield",
                            "heading" => __("Title", 'traveler'),
                            "param_name" => "title",
                            "description" => __("Enter paragraph title.", 'traveler')
                        ],
                        [
                            "type" => "attach_image",
                            "heading" => __("Image", 'traveler'),
                            "param_name" => "iamge",
                            "description" => __("Enter your image.", 'traveler')
                        ],
                        [
                            "type" => "dropdown",
                            "heading" => __("Position", 'traveler'),
                            "param_name" => "position",
                            "description" => __("Select position.", 'traveler'),
                            "value" => array(
                                __("Left to Right", 'traveler') => "ltr",
                                __("Right to Left", 'traveler') => "rtl"
                            )
                        ],
                        [
                            "type" => "textarea",
                            "heading" => __("Body", 'traveler'),
                            "param_name" => "body",
                            "description" => __("Enter your paragraph", 'traveler'),
                        ],
                    ),
                ],
            ]
        )
    );
    vc_map(
        array(
            'name' => 'Passionate team',
            'base' => 'passionate_team',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => 'Tilte',
                    'param_name' => 'title',
                    'description' => 'Enter the Passionate title',
                ),

                array(
                    'type' => 'textfield',
                    'heading' => 'Description',
                    'param_name' => 'description',
                    'description' => 'Enter the Passionate description',
                ),

                array(
                    "type" => "param_group",
                    "heading" => __("Member"),
                    "param_name" => "member",
                    'description' => 'Enter the member',
                    'params' => array(
                        [
                            "type" => "textfield",
                            "heading" => __("Name", 'traveler'),
                            "param_name" => "name",
                            "description" => __("Enter name", 'traveler')
                        ],
                        [
                            "type" => "attach_image",
                            "heading" => __("Avatar", 'traveler'),
                            "param_name" => "avatar",
                            "description" => __("Enter your avatar.", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Position", 'traveler'),
                            "param_name" => "position",
                            "description" => __("Enter your position.", 'traveler'),
                        ],
                    )
                ),

                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
    vc_map(
        array(
            'name' => 'Trip option',
            'base' => 'trip_option',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => 'Tilte',
                    'param_name' => 'title',
                    'description' => 'Enter the Option title',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Description',
                    'param_name' => 'description',
                    'description' => 'Enter the Option description',
                ),

                array(
                    "type" => "attach_image",
                    "heading" => __("Background"),
                    "param_name" => "background",
                    'description' => 'Enter the Option background',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Text Button',
                    'param_name' => 'text_btn',
                    'description' => 'Enter the introduction title',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Link button',
                    'param_name' => 'link_btn',
                    'description' => 'Enter the introduction title',
                ),

                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
  
    vc_map(
        array(
            'name' => 'Page banner',
            'base' => 'page_banner',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => 'Tilte',
                    'param_name' => 'title',
                    'description' => 'Enter the banner title',
                ),

                array(
                    "type" => "attach_image",
                    "heading" => __("Background"),
                    "param_name" => "background",
                    'description' => 'Enter the banner background',
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => 'Disable Plan Field',
                    'param_name' => 'disable_plan_field',
                    'value' => '',
                    'description' => 'You want disable trip plan?',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Title plan',
                    'param_name' => 'title_plan',
                    'description' => 'Enter the Title of plan',
                ),

                array(
                    'type' => 'param_group',
                    'heading' => 'Step of plan',
                    'param_name' => 'step_of_plan',
                    'description' => 'Enter the steps',
                    'params' => array(
                        [
                            "type" => "textfield",
                            "heading" => __("Step title", 'traveler'),
                            "param_name" => "step_title",
                            "description" => __("Enter step title", 'traveler')
                        ],
                        [
                            "type" => "attach_image",
                            "heading" => __("Step icon", 'traveler'),
                            "param_name" => "step_icon",
                            "description" => __("Enter your avatar.", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Step content", 'traveler'),
                            "param_name" => "step_content",
                            "description" => __("Enter your position.", 'traveler'),
                        ],
                    )
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Text button',
                    'param_name' => 'text_btn',
                    'description' => 'Enter the introduction title',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => 'Link button',
                    'param_name' => 'link_btn',
                    'description' => 'Enter the introduction title',
                ),

                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
    vc_map(
        array(
            'name' => 'Client slider',
            'base' => 'client_slider',
            'category' => 'BookYourTravel',
            'params' => array(
                array(
                    'type' => 'param_group',
                    'heading' => 'Logo element',
                    'param_name' => 'logo_element',
                    'description' => 'Enter the logo title',
                    'params' => array(
                        [
                            "type" => "textfield",
                            "heading" => __("Title", 'traveler'),
                            "param_name" => "title",
                            "description" => __("Enter step title", 'traveler')
                        ],
                        [
                            "type" => "attach_image",
                            "heading" => __("Icon", 'traveler'),
                            "param_name" => "icon",
                            "description" => __("Enter your avatar.", 'traveler')
                        ],
                        [
                            "type" => "textfield",
                            "heading" => __("Link icon", 'traveler'),
                            "param_name" => "link_icon",
                            "description" => __("Enter your avatar.", 'traveler')
                        ],
                    )
                ),
                // Thêm các trường tùy chỉnh khác cho thành phần của bạn
            ),
        )
    );
}
