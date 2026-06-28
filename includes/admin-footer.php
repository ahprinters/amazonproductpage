        </main>

        <footer class="bg-white border-t border-slate-200 px-6 py-4 text-sm text-slate-500">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <p>
                    © <?= date('Y') ?> Amazon Admin Panel
                </p>

                <p>
                    Built with Tailwind CSS
                </p>
            </div>
        </footer>

    </div>

</div>

<script>
    function openMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');

        if (sidebar) {
            sidebar.classList.remove('hidden');
        }
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');

        if (sidebar) {
            sidebar.classList.add('hidden');
        }
    }
</script>

</body>
</html>