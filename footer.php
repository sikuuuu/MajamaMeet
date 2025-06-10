</div> <!-- Content End -->
</div> <!-- Main End -->

<!-- Footer -->
<footer style="background:#222; color:white; text-align:center; padding:15px;">
    &copy; <?php echo date("Y"); ?> MajamaMeet | Shrikant Rajgor Tech Pvt Ltd<br>
    Contact: 
    <a href="mailto:shrikantrajgor0@gmail.com" style="color:#00BFA6;">shrikantrajgor0@gmail.com</a> | 
    📞 8849051678 | <br>
    <a href="https://instagram.com/majamaMeet_" target="_blank" style="color:#00BFA6; text-decoration:none;">
         Follow us on Instagram (@majamaMeet_)
    </a><br>
    <?php echo date("l, F j, Y, g:i A"); ?>
</footer>

<!-- Sidebar & Dark Mode Script -->
<script>
const toggleBtn = document.getElementById('toggleBtn');
const sidebar = document.getElementById('sidebar');
const modeSwitch = document.getElementById('modeSwitch');

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
});

modeSwitch.addEventListener('change', () => {
    document.body.classList.toggle('dark-mode');
});
</script>

</body>
</html>
