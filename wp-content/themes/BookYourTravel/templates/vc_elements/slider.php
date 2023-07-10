<?php
$gallerys = vc_param_group_parse_atts($attr['gallery']);

// var_dump($gallerys);

?>
<style>
    .custom-slider-wrap {
        width: 100%;
        overflow: hidden;
        height: 400px;
        position: relative;
    }

    .custom-sliders {
        width: 100%;
        height: 400px;
        display: flex;
        transition: 0.5s all;

    }

    .slider-item {
        width: 100%;
        flex: 0 0 100%;
        transition: 0.5s all;
    }

    .slider-item img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        object-position: center;
    }

    .slider-item::after {
        position: absolute;
    }
</style>

<div class="custom-slider-wrap">
    <div class="custom-sliders">
        <?php
        foreach ($gallerys as $gallery) {
            $image_id = $gallery['image'];
            $image_url = wp_get_attachment_image_src($image_id, 'full');
            // var_dump($image_id);
        ?>
            <div class="slider-item">
                <img src="<?= $image_url[0] ?>" alt="">
            </div>
        <?php
        }
        ?>
        <div class="slider-title">
            <p>
                    
                    <?= $attr['title'] ?>
            </p>
        </div>
    </div>

</div>
<script>
    const slider = document.querySelector('.custom-sliders');
    const slides = slider.querySelectorAll('.slider-item');
    const slideWidth = slides[0].offsetWidth;
    const itemCount = slides.length;
    console.log(slideWidth)
    let currentIndex = 0;

    function moveToSlide(index) {
        if (index < 0 || index >= itemCount) {
            return;
        }
        slider.style.transform = `translateX(-${index * slideWidth}px)`;
        currentIndex = index;

    }

    function moveNext() {
        const currentSlide = slider.querySelector('.slider-item:nth-child(' + (currentIndex + 1) + ')');
        moveToSlide(currentIndex + 1);
    }

    // Move to the next slide every 3 seconds
    // setInterval(moveNext, 5000);
</script>