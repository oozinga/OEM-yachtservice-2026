<footer class="oem-footer">
  <div class="oem-footer__inner">
    <div class="oem-footer__grid">
      <div>
        <div class="oem-footer__logo">
          <img src="<?php echo esc_url(oem_get_logo_url('white')); ?>" alt="OEM Yacht Service">
        </div>
        <p class="oem-footer__tagline">Independent yacht support, in combination with the original equipment manufacturers' knowledge.</p>
      </div>
      <div>
        <h5 class="oem-footer__heading">What we do</h5>
        <?php
        $services = oem_get_services();
        foreach ($services as $s) :
        ?>
          <a href="<?php echo esc_url(home_url('/what-we-do/' . $s['slug'] . '/')); ?>" class="oem-footer__link"><?php echo esc_html($s['title']); ?></a>
        <?php endforeach; ?>
      </div>
      <div>
        <h5 class="oem-footer__heading">Projects</h5>
        <a href="<?php echo esc_url(home_url('/projects/')); ?>" class="oem-footer__link">All projects</a>
        <a href="<?php echo esc_url(home_url('/oem-connect/')); ?>" class="oem-footer__link">OEM Connect</a>
        <a href="<?php echo esc_url(home_url('/careers/')); ?>" class="oem-footer__link">Careers</a>
      </div>
      <div>
        <h5 class="oem-footer__heading">About</h5>
        <a href="<?php echo esc_url(home_url('/team/')); ?>" class="oem-footer__link">Team</a>
        <a href="<?php echo esc_url(home_url('/team-sub/')); ?>" class="oem-footer__link">Team Sub</a>
      </div>
      <div>
        <h5 class="oem-footer__heading">Contact</h5>
        <a href="mailto:info@oemyachtservice.com" class="oem-footer__link">info@oemyachtservice.com</a>
        <a href="tel:+31611004005" class="oem-footer__link">+31 (0) 6 1100 4005</a>
        <p class="oem-footer__address">Industriepark 10<br>8701 PN Bolsward</p>
      </div>
    </div>
    <div class="oem-footer__bottom">
      <div class="oem-footer__legal">
        <a href="<?php echo esc_url(home_url('/contact/')); ?>">Terms &amp; conditions</a>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>">Privacy</a>
      </div>
      <span class="oem-footer__copyright">&copy; 2026 OEM Yacht Service B.V.</span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
