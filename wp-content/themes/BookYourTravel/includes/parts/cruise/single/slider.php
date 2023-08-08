<style>
    .slides {
        display: flex;
        height: 100%;
    }

    .slide {
        min-width: 100%;
        position: relative;
        filter: opacity(0.4);
    }

    .active {
        filter: unset
    }

    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .slide-controls {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .slide-controls svg {
        width: 20px;
        color: #fff;
        fill: #fff;
    }

    #next-btn,
    #prev-btn {
        cursor: pointer;
        background: transparent;
        font-size: 30px;
        border: none;
        padding: 10px;
        color: white;
    }

    #next-btn:focus,
    #prev-btn:focus {
        outline: none;
    }

    .slide-content {
        position: absolute;
        top: 50%;
        left: 50px;
        transform: translateY(-50%);
        font-size: 60px;
        color: white;
    }

    #post-slider {
        position: relative;
    }
</style>
<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_cruise_tabs, $entity_obj, $layout_class, $tab, $bookyourtravel_theme_post_types;

$cruise_obj = new BookYourTravel_Cruise($post);
$entity_obj = $cruise_obj;

$images = $entity_obj->get_images();
if ($images && count($images) > 0) {

?>
    <div id="post-slider" class="nano-container">
        <div class="slides">
            <?php
            $image_sources = array();
            for ($i = 0; $i < count($images); $i++) {
                $image = $images[$i];
                $image_id = $image['image'];
                $image_src = wp_get_attachment_image_src($image_id, '1920x600');
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                $image_src = $image_src && is_array($image_src) && count($image_src) > 0 ? $image_src[0] : '';
                if (!empty($image_src)) {
            ?>
                    <div class="slide">
                        <img src="<?= $image_src ?>" alt="" />
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <div class="slide-controls">
            <button id="prev-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z" />
                </svg>
            </button>
            <button id="next-btn">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                    <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                </svg>
            </button>
        </div>
    </div>


<?php

}

?>
<!-- <script>
    const slideContainer = document.querySelector('#post-slider');
    const slide = document.querySelector('.slides');
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const interval = 3000;

    let slides = document.querySelectorAll('.slide');
    let index = 1;
    let slideId;

    const firstClone = slides[0].cloneNode(true);
    const lastClone = slides[slides.length - 1].cloneNode(true);

    firstClone.id = 'first-clone';
    lastClone.id = 'last-clone';

    slide.append(firstClone);
    slide.prepend(lastClone);

    const slideWidth = slides[index].clientWidth;

    slide.style.transform = `translateX(${-slideWidth * index}px)`;

    console.log(slides);

    const startSlide = () => {
        slideId = setInterval(() => {
            moveToNextSlide();
        }, interval);

        slides[0].classList.add('active');
    };

    const getSlides = () => document.querySelectorAll('.slide');

    slide.addEventListener('transitionend', () => {
        slides = getSlides();
        if (slides[index].id === firstClone.id) {
            slide.style.transition = 'none';
            index = 1;
            slide.style.transform = `translateX(${-slideWidth * index}px)`;
        }

        if (slides[index].id === lastClone.id) {
            slide.style.transition = 'none';
            index = slides.length - 2;
            slide.style.transform = `translateX(${-slideWidth * index}px)`;
        }
    });

    const moveToNextSlide = () => {
        slides = getSlides();
        if (index >= slides.length - 1) return;
        slides[index].classList.remove('active');
        index++;
        slide.style.transition = '.7s ease-out';
        slide.style.transform = `translateX(${-slideWidth * index}px)`;
        // Add "active" class to the next slide
        if (index < slides.length - 1) {
            slides[index].classList.add('active');
        }
    };

    const moveToPreviousSlide = () => {
        if (index <= 0) return;
        slides[index].classList.remove('active');
        index--;
        slide.style.transition = '.7s ease-out';
        slide.style.transform = `translateX(${-slideWidth * index}px)`;
        // Add "active" class to the next slide
        if (index > 0) {
            slides[index].classList.add('active');
        }
    };

    slideContainer.addEventListener('mouseenter', () => {
        clearInterval(slideId);
    });

    // slideContainer.addEventListener('mouseleave', startSlide);
    nextBtn.addEventListener('click', moveToNextSlide);
    prevBtn.addEventListener('click', moveToPreviousSlide);

    startSlide();
</script> -->