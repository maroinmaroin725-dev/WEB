/* Search and Filter Functionality */

class SearchManager {
    constructor(inputSelector, resultsSelector) {
        this.input = document.querySelector(inputSelector);
        this.resultsContainer = document.querySelector(resultsSelector);
        this.init();
    }

    init() {
        if(this.input) {
            this.input.addEventListener('input', debounce(() => this.search(), 300));
        }
    }

    async search() {
        const query = this.input.value.trim();

        if(query.length < 2) {
            this.resultsContainer.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/api/search.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            if(data.success) {
                this.displayResults(data.results);
            }
        } catch(error) {
            console.error('Search error:', error);
        }
    }

    displayResults(results) {
        if(results.length === 0) {
            this.resultsContainer.innerHTML = '<p class="text-center">No results found</p>';
            return;
        }

        let html = '<div class="search-results">';
        results.forEach(result => {
            html += `
                <div class="search-result-item">
                    <h4>${result.name}</h4>
                    <p>${result.description || ''}</p>
                    <a href="${result.link}" class="btn btn-sm btn-primary">View</a>
                </div>
            `;
        });
        html += '</div>';

        this.resultsContainer.innerHTML = html;
    }
}

// Initialize search if elements exist
if(document.querySelector('[data-search-input]')) {
    window.searchManager = new SearchManager(
        '[data-search-input]',
        '[data-search-results]'
    );
}
