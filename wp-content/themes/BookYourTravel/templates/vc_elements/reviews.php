<?php
$img = $attr['background_image'];
$img_url = wp_get_attachment_image_src($img, 'full');
?>

<div class="reviews-wrap">
    <div class="reviews">
        <div class="reviews-top">
            <div class="reviews-title">
                <?= $attr['title'] ?>
            </div>
            <div class="reviews-description">
                <?= $attr['description'] ?>
            </div>
        </div>
        <div class="reviews-body">
            <div class="slider-controls">
                <button class="slider-prev">
                </button>
                <button class="slider-next">
                </button>
            </div>
            <div class="reviews-slider">

                <?php
                $args = array(
                    'post_type' => 'review',
                    'posts_per_page' => -1, // Retrieve all posts
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $likes = get_post_meta(get_the_ID(), 'review_likes', true);
                        $name = get_the_title();
                        $post_id = get_the_ID();

                        // Lấy các đánh giá (ratings)
                        $rating = get_post_meta($post_id, '', true);
                        $review_overall = $rating['review_overall'];
                        $overrall = intval($review_overall[0]);
                        $date =  get_the_date();
                ?>

                        <article class="reivews-item">
                            <div class="reviews-item-top">
                                <img src="<?php echo get_template_directory_uri(); ?>/css/images/no-avt.jpg" alt="" class="avatar-img">
                                <div class="reivews-info">
                                    <div class="reivews-author"><?php echo $name; ?></div>
                                    <div class="reivews-date"><?php echo $date; ?></div>
                                </div>
                            </div>
                            <div class="reivews-content-wrap">
                                <div class="overrall">
                                    <?php
                                    if ($overrall >= 8) {
                                    ?>
                                        <p>
                                            <i class="star"></i>
                                            <i class="star"></i>
                                            <i class="star"></i>
                                            <i class="star"></i>
                                            <i class="star"></i>
                                        </p>
                                    <?php
                                    } else if ($overrall <= 7 && $overrall >= 4) {
                                    ?>
                                        <p>
                                            <i class="star"></i>
                                            <i class="star"></i>
                                            <i class="star"></i>
                                        </p>
                                    <?php
                                    } else if ($overrall <= 3) {
                                    ?>
                                        <p>
                                            <i class="star"></i>
                                        </p>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <blockquote class="reivews-content"><?php echo $likes; ?></blockquote>
                                <div class="read-more-btn">
                                    Read more
                                </div>
                            </div>

                        </article>

                <?php
                    }
                    wp_reset_postdata(); // Reset the query
                } else {
                    // No reviews found
                }
                ?>

            </div>


        </div>
    </div>
    <div class="thumbnail" style="flex: 1">
        <img src="<?= $img_url[0] ?>" alt="">
    </div>
</div>

<script>
    const slider = document.querySelector('.reviews-slider');
    const prevButton = document.querySelector('.slider-prev');
    const nextButton = document.querySelector('.slider-next');
    const slides = slider.querySelectorAll('.reivews-item');
    const slideWidth = slides[0].offsetWidth;
    const itemCount = slides.length;
    console.log(slideWidth, 'slideWidth')
    console.log('itemCount', itemCount)
    let currentIndex = 0;

    function moveToSlide(index) {
        if (index < 0 || index >= itemCount) {
            return;
        }
        slider.style.transform = `translateX(-${index * slideWidth}px)`;
        currentIndex = index;

        // Hide đoạn văn khi di chuyển slide
        const slides = Array.from(slider.querySelectorAll('.reivews-item'));
        slides.forEach((slide, i) => {
            const content = slide.querySelector('.reivews-content');
            const readMoreButton = slide.querySelector('.read-more-btn');

            if (i !== currentIndex) {
                content.classList.remove('show-more');
                readMoreButton.textContent = 'Read more';
            }
        });
    }

    prevButton.addEventListener('click', () => {
        moveToSlide(currentIndex - 1);
    });

    nextButton.addEventListener('click', () => {
        // Hide đoạn văn trước khi di chuyển slide
        const currentSlide = slider.querySelector('.reivews-item:nth-child(' + (currentIndex + 1) + ')');
        const content = currentSlide.querySelector('.reivews-content');
        const readMoreButton = currentSlide.querySelector('.read-more-btn');

        content.classList.remove('show-more');
        readMoreButton.textContent = 'Read more';

        moveToSlide(currentIndex + 1);
    });

    const readMoreButtons = document.querySelectorAll('.read-more-btn');

    readMoreButtons.forEach(button => {
        button.addEventListener('click', () => {
            const content = button.previousElementSibling;
            content.classList.toggle('show-more');
            button.textContent = content.classList.contains('show-more') ? 'Hide' : 'Read more';
        });
    });

    // function moveNext() {
    //     const currentSlide = slider.querySelector('.reivews-item:nth-child(' + (currentIndex + 1) + ')');
    //     const content = currentSlide.querySelector('.reivews-content');
    //     const readMoreButton = currentSlide.querySelector('.read-more-btn');

    //     content.classList.remove('show-more');
    //     readMoreButton.textContent = 'Read more';

    //     moveToSlide(currentIndex + 1);
    // }

    // // Move to the next slide every 3 seconds
    // setInterval(moveNext, 5000);
</script>