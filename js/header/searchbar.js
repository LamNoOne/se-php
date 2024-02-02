const toggleSearch = (search, button) => {
    const searchBar = document.getElementById(search)
    const searchButton = document.getElementById(button)

    searchButton.addEventListener('click', () => {
        searchBar.classList.toggle('show-search')
    })
}

toggleSearch('search-bar', 'search-button')