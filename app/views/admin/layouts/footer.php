      </div> <!-- /.container-fluid -->
    </main>
  </div> <!-- /.admin-wrap -->

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var toggle = document.getElementById('sidebarToggle');
      var sidebar = document.getElementById('adminSidebar');
      if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
          sidebar.classList.toggle('open');
        });
      }
    });
  </script>
</body>
</html>
