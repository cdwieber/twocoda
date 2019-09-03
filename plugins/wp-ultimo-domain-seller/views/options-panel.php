<style>
#wu-product-data ul.wc-tabs li.domain_registration_options a:before, 
#wu-coupon-data ul.wc-tabs li.domain_registration_options a:before, 
.wu ul.wc-tabs li.domain_registration_options a:before {
  content: "\f103";
}
</style>

<div id="wu_domain_registration" class="panel wu_options_panel">

    <div class="options_group">

      <div class="options_group">
        <p class="form-field domain_registration_field">
          <label for="domain_registration"><?php _e('Domain Registration', 'wu-domain-seller'); ?></label> 
          <input <?php checked($plan->domain_registration); ?> type="checkbox" name="domain_registration" id="domain_registration" value="1" class="checkbox"> 
          <span class="description"><?php _e('Enable clients of this plan to register a custom domain while signing up for an account.', 'wu-domain-seller'); ?></span>
        </p>
      </div>

  </div>
  
</div>