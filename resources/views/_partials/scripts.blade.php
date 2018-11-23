<script src="{{ asset('js/app.js') }}"></script>
{{-- <script src="{{ asset('assets/semantic/dist/semantic.min.js') }}"></script>
<script src="{{ asset('assets/semantic/dist/tablesort.js') }}"></script> --}}
<script>
$(document).ready(function() {
  // fix menu when passed
  $('.masthead').visibility({
    once: false,
    onBottomPassed: function() {
      $('.fixed.menu').transition('fade in');
    },
    onBottomPassedReverse: function() {
      $('.fixed.menu').transition('fade out');
    }
  });
  // create sidebar and attach to menu open
  $('.ui.sidebar').sidebar('attach events', '.toc.item');

  $('.ui.dropdown').dropdown();
  $('.shape').shape();
  $('.boundary.example .button').popup({
    boundary: '.boundary.example .segment'
  });

  setTimeout(() => {
    $('#home-header').fadeIn('fast', function() {
      $(this).transition('tada');
    });
  },600);
  setTimeout(() => {
    $('#home-sub-header').fadeIn('fast', function() {
      $(this).transition('bounce');
    });
    
  }, 800);
  // setInterval(() => {
  //   $('#home-header').transition('tada');
  // }, 3500);
  $('.special.cards .image').dimmer({
    on: 'hover'
  });
  $('.sortable.table').tablesort();
  $('.tabular.menu .item').tab();
  $('.pointing.secondary.menu .item').tab();
  $('.ui.checkbox').checkbox();
});
</script>