<?php get_header(); ?>
<section class="hero">
    <a href="http://localhost/wordpress/shop/" class="hero__wrapper">
        <div class="wrapper">
            <div class="wrapper__heading">
                <span>THE NEW GAME COLLECTION</span>
                <h1>ALL GAMES</h1>
                <div class="button button-white">BROWSE GAMES</div>
            </div>
        </div>
    </a>
</section>



<section class="shop-section">
    <h2 class="shop-section__heading">New Arrivals</h2>
    <p class="shop-section__body">New arrivals. Updated every<br/> day. It's time to explore.</p>
    <div class="items">
        <?php echo do_shortcode('[products limit="4" orderbyid="id" order="DESC"]'); ?>
    </div>
</section>

<section class="newsletter">

<h1 class="newsletter__heading">Sign up to GameZone newsletter and
    get updates about our latest arrivals
    and exclusive sales.</h1>

    <form class="form" action="#" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate="">
        <input class="form__email" type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required="">
            <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
        <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_c12aafb941c4276911b0d5cd3_839b42c955" tabindex="-1" value=""></div>
        <input class="form__submit button" type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe">
    </form>

</section>



<?php get_footer(); ?>