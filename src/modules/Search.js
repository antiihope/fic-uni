class Search {
  constructor() {
    this.addSearchHTML();
    this.resultsDiv = document.querySelector('#search-overlay__results');
    this.openButton = document.querySelector('.js-search-trigger');
    this.closeButton = document.querySelector('.search-overlay__close');
    this.searchOverlay = document.querySelector('.search-overlay');
    this.searchField = document.querySelector('#search-term');
    this.events();
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
    console.log('Search module loaded');
  }

  anyIsFocused() {
    return document.activeElement.tagName === 'INPUT';
  }

  events() {
    this.openButton.addEventListener('click', this.openOverlay.bind(this));
    this.closeButton.addEventListener('click', this.closeOverlay.bind(this));
    document.addEventListener('keydown', this.keyPressDispatcher.bind(this));
    this.searchField.addEventListener('keyup', this.typingLogic.bind(this));
  }

  getJson(type = 'posts') {
    return new Promise((resolve, reject) => {
      fetch(uniData.root_url + `/wp-json/wp/v2/${type}?search=` + this.searchField.value)
        .then((response) => response.json())
        .then((data) => {
          resolve(data);
        });
    });
  }

  async getResults() {
    const Posts = this.getJson('posts');
    const Pages = this.getJson('pages');
    Promise.all([Pages, Posts]).then((pages, posts) => {
      const data = pages.concat(posts);
      this.resultsDiv.innerHTML = `
        <h2 class="search-overlay__section-title">General Information</h2>
        ${data.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
          ${data
            .map(
              (item) => `
            <li><a href="${item.link}">${item.title.rendered}</a></li>
          `
            )
            .join('')}
        
        `;
    });
    this.isSpinnerVisible = false;
  }

  typingLogic() {
    if (this.searchField.value != this.previousValue) {
      clearTimeout(this.typingTimer);

      if (this.searchField.value) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>';
          this.isSpinnerVisible = true;
        }

        this.typingTimer = setTimeout(() => this.getResults(), 750);
      } else {
        this.resultsDiv.innerHTML = '';
        this.isSpinnerVisible = false;
      }
    }

    this.previousValue = this.searchField.value;
  }

  keyPressDispatcher(e) {
    if (e.keyCode === 83 && !this.isOverlayOpen && !this.anyIsFocused()) {
      this.openOverlay();
    }

    if (e.keyCode === 27 && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  openOverlay() {
    this.searchOverlay.classList.add('search-overlay--active');
    document.body.classList.add('body-no-scroll');
    this.searchField.value = '';
    setTimeout(() => {
      this.searchField.focus();
    }, 350);
    this.isOverlayOpen = true;
    return false;
  }

  closeOverlay() {
    this.searchOverlay.classList.remove('search-overlay--active');
    document.body.classList.remove('body-no-scroll');
    this.isOverlayOpen = false;
  }

  addSearchHTML() {
    document.body.insertAdjacentHTML(
      'beforeend',
      `
    <div class="search-overlay">
      <div class="search-overlay__top">
        <div class="container">
          <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
          <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
          <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
        </div>
      </div>
      <div class="container">
        <div id="search-overlay__results"></div>
      </div>
    </div>
    `
    );
  }
}
export default Search;
