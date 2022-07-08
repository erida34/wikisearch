document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tabs-nav a').forEach(function (tabLink) {
        tabLink.addEventListener('click', function (event) {
            const path = event.target.href.split('#')[1];
            document.querySelectorAll('.tabs-nav a').forEach(function (tabContent) {
                tabContent.classList.remove('active')
            })
            event.target.classList.add("active")
            document.querySelectorAll('.box').forEach(function (tabContent) {
                tabContent.classList.remove('box_active')
            })
            document.querySelector('#block-'+path).classList.add('box_active')
        })
    })
})