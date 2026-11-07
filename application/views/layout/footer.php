  </main>
</div>

<script>
  // Mobile sidebar toggle script
  (function() {
    var burger = document.getElementById('burger');
    var side = document.getElementById('side');
    var overlay = document.getElementById('sideOverlay');

    function toggleMenu() {
      if (side) side.classList.toggle('buka');
      if (overlay) overlay.classList.toggle('active');
    }

    if (burger) burger.addEventListener('click', toggleMenu);
    if (overlay) overlay.addEventListener('click', toggleMenu);
  })();
</script>
</body>
</html>