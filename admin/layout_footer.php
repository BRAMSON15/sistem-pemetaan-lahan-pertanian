        </div><!-- /.content-area -->
    </div><!-- /.main-content -->
</div><!-- /.wrapper -->

<!-- Scripts -->
<script src="../assets/template/assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/template/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js (Dummy data for mockup) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<!-- Leaflet Fullscreen JS -->
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>

<script>
    // Responsive Sidebar Toggle Logic
    document.addEventListener('DOMContentLoaded', function() {
        var sidebarToggle = document.getElementById('sidebarToggle');
        var sidebar = document.querySelector('.sidebar');
        var sidebarOverlay = document.getElementById('sidebarOverlay');

        if(sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                this.classList.remove('active');
            });
        }
        
        // Enhanced Delete Confirmation
        document.querySelectorAll('a[onclick*="confirm"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var message = this.getAttribute('onclick').match(/confirm\('([^']+)'\)/)[1];
                
                if(confirm('⚠️ Konfirmasi Hapus\n\n' + message + '\n\nTindakan ini tidak dapat dibatalkan!')) {
                    window.location.href = this.href;
                }
            });
            
            // Remove the inline onclick to prevent double confirmation
            link.removeAttribute('onclick');
        });
        
        // Button Hover Effects
        document.querySelectorAll('.btn').forEach(function(btn) {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'all 0.2s ease';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Table Row Hover Effects
        document.querySelectorAll('.table tbody tr').forEach(function(row) {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(46, 125, 50, 0.1)';
                this.style.transition = 'background-color 0.2s ease';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    });
</script>

<!-- Render Scripts specific to page -->
<?php if(isset($extra_scripts)) echo $extra_scripts; ?>

</body>
</html>
