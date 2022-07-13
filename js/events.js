
function create_events_btn(block) {// создание функций для кнопок в блоке block
    document.querySelectorAll('.' + block + '_block .btn_more').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            var page_id = event.target.value
            document.querySelector('.modal-wrap').classList.remove('modal-close') // открытие модального окна
            document.querySelector('.modal-wrap h3').innerHTML = ''
            document.querySelector('.modal-wrap p').innerHTML = ''
            document.querySelector('#modal_load').classList.remove('modal-close')
            fetch(`https://ru.wikipedia.org/w/api.php?origin=*&action=query&format=json&pageids=${page_id}&prop=pageimages|info|extracts&piprop=original`) // делаем запрос в api wiki
                .then(response => response.json())
                .then(data => {
                    result = data['query']['pages'][page_id]
                    document.querySelector('.modal-wrap h3').innerHTML = result.title
                    document.querySelector('.modal-wrap p').innerHTML = result.extract
                    document.querySelector('#modal_load').classList.add('modal-close')
                })
        })
    })
    document.querySelectorAll('.' + block + '_block .btn_save').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            var page_id = event.target.value
            let data = new FormData();
            data.append("page_id", page_id);
            fetch('handlers/save_page.php', {
                method: 'POST',
                body: data
            }) // делаем запрос в api wiki
                .then(response => response.text())
                .then(() => {
                    alert('Запись сохранена')
                    generate_import_block() // обновляем блок с загруженными страницами
                })
        })
    })
}

function generate_import_block() {
    fetch('handlers/get_pages.php') // делаем запрос в api wiki
        .then(response => response.json()) // читаем ответ как json
        .then(data => {
            document.querySelector('.import_block').innerHTML = ''
            data.forEach(function (elem) {
                document.querySelector('.import_block').innerHTML += create_import_res(elem);
            })
            create_events_btn('import')
        });
}
generate_import_block() // получение всех записей из бд при загрузке страницы

document.querySelector('.modal-wrap').addEventListener('click', function (event) { // закрытие модального окна
    if (event.target.classList.contains('modal-wrap')) // нажатие именно на серую область
        document.querySelector('.modal-wrap').classList.add('modal-close')
})

document.querySelector('#inp-search').addEventListener('input', function () {
    document.querySelector('#search_load').classList.remove('modal-close')
    fetch(`https://ru.wikipedia.org/w/api.php?origin=*&action=query&format=json&list=search&srlimit=20&srsearch=${this.value}`) // делаем запрос в api wiki
        .then(response => response.json()) // читаем ответ как json
        .then(data => {
            document.querySelector('.search_block').innerHTML = ''
            data.query.search.forEach(function (elem) {
                document.querySelector('.search_block').innerHTML += create_search_res(elem);
            })
            document.querySelector('#search_load').classList.add('modal-close')
            create_events_btn('search');// создание функций для кнопок
        });
})

document.querySelector('#inp-import').addEventListener('input', function () {
    if (this.value != '') { // если запросили не пустую строку
        document.querySelector('#import_load').classList.remove('modal-close')
        let datas = new FormData();
        datas.append("word", this.value);
        fetch('handlers/search.php', {
            method: 'POST',
            body: datas
        }) // делаем запрос в api wiki
            .then(response => response.json()) // читаем ответ как json
            .then(data => {
                document.querySelector('.import_block').innerHTML = ''
                data.forEach(function (elem) {
                    document.querySelector('.import_block').innerHTML += create_import_res(elem);
                })
                document.querySelector('#import_load').classList.add('modal-close')
                create_events_btn('import');// создание функций для кнопок
            });
    }
    else { // если запросили пустую строку, то лучше просто получить все записи
        generate_import_block()
    }
})