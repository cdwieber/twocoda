(function($) {
  $(document).ready(function() {
    domain_search = new Vue({
      el: "#loginform",
      data: {
        search: wu_dm_search.search,
        selected_domain: wu_dm_search.selected_domain,
        loading: false,
        results: [],
        current_request: null,
      },
      watch: {
        search: function(term) {
          this.selected_domain = '';
          this.search_domain();
        }
      },
      mounted: function() {
        this.search_domain();
      },
      methods: {
        set_domain: function(domain) {
          if (!domain.available) return;
          this.selected_domain = domain.domain;
        },
        is_selected: function(domain) {
          return domain.domain == this.selected_domain;
        },
        search_domain: function() {
          var that = this;
          that.results = [];

          if (that.search.length == 0) return;

          that.loading = true;
          that.current_request = $.ajax({
            url: wu_dm_search.ajaxurl,
            data: {
              action: "wu_domain_selling",
              do: "lookup_domain",
              domain: that.search,
              wpnonce: wu_dm_search.wpnonce
            },
            beforeSend: function() {
              if (that.current_request != null) {
                that.current_request.abort();
              }
            },
            success: function(results) {
              if (results.success && results.data.constructor.name == "Array") {
                that.results = results.data;
              }
              that.loading = false;
              that.current_request = null;
            }
          });
        }
      }
    });
  });
})(jQuery);