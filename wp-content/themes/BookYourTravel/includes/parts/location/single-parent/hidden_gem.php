<?php
$hidden_gem = get_query_var('hidden_gem');
if ($hidden_gem) {
?>
    <section id="hidden_gem" class="section">
        <div class="hidden_gem-wrap nano-container">
            <div class="hidden_gem-left">
                <h2><?= the_title() ?> Hidden Gem</h2>
                <div class="hidden_gem-content">
                    <div id="firstChar" class="hidden_gem-top-paragraph ">
                        <?= $hidden_gem['show_paragraphs'] ?>
                    </div>
                    <div data-sate = "more" class="hidden_gem-action-btn">
                        Show more
                    </div>
                    <div class="hidden_gem-bottom-paragraph">
                        <?= $hidden_gem['hidden_paragraphs'] ?>
                    </div>
                </div>
                <div class="country-information">
                    <div class="world_heritage country-information-item">
                        <div class="number-item">
                            <?= $hidden_gem['world_heritage']  ?>
                        </div>
                        <p>
                            World Heritage Site
                        </p>
                    </div>
                    <div class="ethnic-minorities country-information-item">
                        <div class="number-item">
                            <?= $hidden_gem['ethnic_minorities']  ?>
                        </div>
                        <p>
                            Ethnic Minorities
                        </p>
                    </div>
                    <div class="cities country-information-item">
                        <div class="number-item">
                            <?= $hidden_gem['cities']  ?>
                        </div>
                        <p>
                            Cities
                        </p>
                    </div>
                </div>
            </div>
            <div class="hidden_gem-right">
                <img src="<?php echo $hidden_gem['image'] ?>" alt="">
            </div>
        </div>
    </section>

<?php
}
?>