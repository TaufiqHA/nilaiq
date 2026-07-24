import 'flowbite';

// Theme Toggle logic
function initThemeToggle() {
    const themeToggleButtons = document.querySelectorAll('.theme-toggle');
    
    function updateToggleIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        const activeButtons = document.querySelectorAll('.theme-toggle');
        activeButtons.forEach(btn => {
            const darkIcon = btn.querySelector('.theme-toggle-dark-icon');
            const lightIcon = btn.querySelector('.theme-toggle-light-icon');
            if (isDark) {
                darkIcon?.classList.add('hidden');
                lightIcon?.classList.remove('hidden');
            } else {
                lightIcon?.classList.add('hidden');
                darkIcon?.classList.remove('hidden');
            }
        });
    }

    updateToggleIcons();

    themeToggleButtons.forEach(btn => {
        // Remove existing listener if any to avoid duplication
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        newBtn.addEventListener('click', function() {
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            updateToggleIcons();
        });
    });
}

// Initialize immediately or on DOMContentLoaded depending on document readyState
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeToggle);
} else {
    initThemeToggle();
}
// Re-initialize theme toggle button if page components change (such as dynamic sidebar loads)
window.initThemeToggle = initThemeToggle;

