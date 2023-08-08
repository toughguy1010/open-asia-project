<?php
$country_weather = get_query_var('weather');
if (isset($country_weather)) {
     $country_id = $country_weather[0];
    $args = [
        'post_type' => 'weather',
        'tax_query' => [
            [
                'taxonomy' => 'region',
                'terms' => $country_id,
                'include_children' => false
            ],
        ],
    ];
    $query = new WP_Query($args);
?>
    <div id="weather_section" class="section">
        <div class="weather-wrapper nano-container">
            <div class="top-weather">
                <img src="<?= get_template_directory_uri() ?>/css/images/icon-weather.webp" alt="">
                <h2>
                    WHEN TO GO & WEATHER
                </h2>
                <?php
                if ($query->have_posts()) {
                    $query->the_post();
                ?>
                    <div class="weather-description">
                        <?= the_content() ?>
                    </div>
            </div>
            <?php

                    $weather_posts = $query->posts;
                    foreach ($weather_posts as $weather_post) {
                        $weather_id = $weather_post->ID;
                        $region_weather = get_field('weather',  $weather_id);
            ?>
                <table class="weather-body">
                    <thead class="cf">
                        <tr>
                            <th class="table-dark-green">REGION</th>
                            <th><span>1</span>Jan</th>
                            <th><span>2</span>Feb</th>
                            <th><span>3</span>Mar</th>
                            <th><span>4</span>Apr</th>
                            <th><span>5</span>May</th>
                            <th><span>6</span>Jun</th>
                            <th><span>7</span>Jul</th>
                            <th><span>8</span>Aug</th>
                            <th><span>9</span>Sep</th>
                            <th><span>10</span>Oct</th>
                            <th><span>11</span>Nov</th>
                            <th><span>12</span>Dec</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($region_weather as $item) {
                        ?>
                            <tr>
                                <th data-title="REGION" class="table-dark-green">
                                    <?= $item['region'] ?>
                                </th>
                                <?php
                                for ($month = 1; $month <= 12; $month++) {
                                    $month_key = strtolower(date("F", mktime(0, 0, 0, $month, 1)));
                                ?>
                                    <td data-title="<?= $month_key?>"><?php
                                       
                                        switch ($item[$month_key][0]) {
                                            case 'Pleasant weather, no rain':
                                        ?>
                                                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-sunny.svg" alt="">
                                            <?php
                                                break;
                                            case 'High heat and humidity':
                                            ?>
                                                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-sun-water.svg" alt="">
                                            <?php
                                                break;
                                            case 'Tropical climate, possible intermittent rain':
                                            ?>
                                                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-sun-cloud-rain.svg" alt="">
                                            <?php
                                                break;
                                            case 'Tropical climate, high chances of rain':
                                            ?>
                                                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-raining.svg" alt="">
                                            <?php
                                                break;
                                            case 'Possible risk of typhoons and storms':
                                            ?>
                                                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-hard-raining.svg" alt="">
                                            <?php
                                                break;
                                            case 'Cool to cold temperature (at night)':
                                            ?>
                                                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-cold.svg" alt="">
                                        <?php
                                                break;
                                            default:
                                                echo '-';
                                        }
                                        ?>
                                    </td>
                                <?php
                                }
                                ?>

                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
        <?php
                    }
                } else {
                    echo 'No weather found';
                }
        ?>
        <div class="weather-bottom">
            <div class="weather-bottom-item">
                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-sunny.svg" alt="">
                <p class="weather-caption">
                    Pleasant weather, no rain
                </p>
            </div>
            <div class="weather-bottom-item">
                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-sun-water.svg" alt="">
                <p class="weather-caption">
                    High heat and humidity
                </p>
            </div>
            <div class="weather-bottom-item">
                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-sun-cloud-rain.svg" alt="">
                <p class="weather-caption">
                    Tropical climate, possible intermittent rain
                </p>
            </div>
            <div class="weather-bottom-item">
                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-raining.svg" alt="">
                <p class="weather-caption">
                    Tropical climate, high chances of rain
                </p>
            </div>
            <div class="weather-bottom-item">
                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-hard-raining.svg" alt="">
                <p class="weather-caption">
                    Possible risk of typhoons and storms
                </p>
            </div>
            <div class="weather-bottom-item">
                <img class="weather-status-icon" src="<?= get_template_directory_uri() ?>/css/images/weather/icon-weather-cold.svg" alt="">
                <p class="weather-caption">
                    Cool to cold temperature (at night)
                </p>
            </div>
        </div>
        </div>
    </div>
<?php
}
?>