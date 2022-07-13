function create_search_res(data) { // создание блока результата поиска
    var result = `
    <div class="search_card mb-20">
        <h3 class="title_small text_midi card__title">${data['title']}</h3>
        <p class="text_small mb-20">${data['snippet']}...</p>
        <button type="button" value="${data['pageid']}" class="text_small text_aver btn btn_more">Смотреть</button>
        <button type="button" value="${data['pageid']}" class="text_small text_aver btn btn_save">Сохранить</button>
    </div>
    `
    return result
}

function create_import_res(data) { // создание блока результата поиска в импорте
    var count_words = (data.hasOwnProperty('count_words') ? `<p>Вхождений: ${data['count_words']}</p>` : '' ) // если идёт поиск, то выводим количество вхождений, иначе просто карточку
    var result = `
    <div class="flex flex-col card card-obzor card_active">
        <img src="${data['img_src']}" alt="img" class="card__img">
        <div class="card__content">
            <h2 class="title_small text_midi card__title">${data['title']}</h2>
            <p class="text_small card__descr">
            ${data['snippet']}
            </p>
            ${count_words}
            <button type="button" value="${data['pageid']}"
                class="text_small text_aver btn btn_more">Смотреть</button>
        </div>
    </div>
    `
    return result
}
