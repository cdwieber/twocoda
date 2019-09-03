<?php wp_enqueue_style('wu-domain-selling-search'); ?>

<ul id="wu-domain-search-results-list">
  <li v-cloak class="wu-domain-result wu-domain-loading" v-if="loading">
    <span class="wu-domain-result-title">
      <?php _e('Searching domains...', 'wu-domain-seller'); ?>
    </span>
    <span class="wu-domain-result-badge">
      <span class="dashicons dashicons-admin-links"></span>
    </span>
  </li>
  <li class="wu-domain-result wu-domain-loading" v-if="!results.length && !loading">
    <span class="wu-domain-result-title">
      <?php _e('Type a domain to search...', 'wu-domain-seller'); ?>
    </span>
    <span class="wu-domain-result-badge">
      <span class="dashicons dashicons-admin-links"></span>
    </span>
  </li>
  <li v-cloak v-for="domain in results" class="wu-domain-result" v-bind:class="{ 'wu-domain-result-taken': !domain.available, 'wu-domain-result-selected': is_selected(domain) }" v-on:click="set_domain(domain)">
    <span class="wu-domain-result-title">{{domain.domain}}</span>

    <span class="wu-domain-result-status" v-if="!domain.available"><?php _e('Taken', 'wu-domain-seller'); ?></span>
    <span class="wu-domain-result-status" v-if="domain.available"><?php _e('Available', 'wu-domain-seller'); ?></span>

    <span class="wu-domain-result-desc" v-if="is_selected(domain)">
      <small><?php printf(__('Your website URL will be <strong>%s</strong>', 'wu-domain-seller'), '{{selected_domain}}'); ?></small>
    </span>

    <span class="wu-domain-result-badge"><?php _e('Free', 'wu-domain-seller'); ?></span>
  </li>
</ul>