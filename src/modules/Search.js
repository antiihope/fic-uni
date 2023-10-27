class Search {
  constructor() {
    this.addSearchHTML();
    this.resultsDiv = document.querySelector('#search-overlay__results');
    this.openButtons = [...document.querySelectorAll('.js-search-trigger')];
    this.closeButton = document.querySelector('.search-overlay__close');
    this.searchOverlay = document.querySelector('.search-overlay');
    this.searchField = document.querySelector('#search-term');
    this.events();
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
  }

  anyIsFocused() {
    return document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA';
  }

  events() {
    this.openButtons.map((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        this.openOverlay();
      });
    });

    this.closeButton.addEventListener('click', this.closeOverlay.bind(this));
    document.addEventListener('keydown', this.keyPressDispatcher.bind(this));
    this.searchField.addEventListener('keyup', this.typingLogic.bind(this));
  }

  getJson(type = 'posts') {
    return new Promise((resolve, reject) => {
      fetch(uniData.root_url + `/wp-json/uni/v1/search?term=` + this.searchField.value)
        .then((response) => response.json())
        .then((data) => {
          resolve(data);
        })
        .catch(() => {
          reject();
        });
    });
  }

  async getResults() {
    const Posts = this.getJson();
    Posts.then((data) => {
      console.log(data);
      this.resultsDiv.innerHTML = `
       <div class="row">
            <div class="one-third">
              <h2 class="search-overlay__section-title">General Information</h2>
                 ${data.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
                 ${data.generalInfo
                   .map(
                     (item) => `
               <li><a href="${item.permalink}">${item.title}</a> 
               ${item.postType === 'post' ? `by ${item.authorName}` : ''}</li>`
                   )
                   .join('')}
            </div>
            <div class="one-third">
              <h2 class="search-overlay__section-title">programs</h2>
              ${data.programs.length ? '<ul class="link-list min-list">' : '<p>No programs matches that search.</p>'}
              ${data.programs.map((item) => `<li><a href="${item.permalink}">${item.title}</a> `).join('')}
              ${data.professors.length ? '</ul>' : ''}


              <h2 class="search-overlay__section-title">professors</h2>
              ${data.professors.length ? '<ul class="professor-cards">' : '<p>No professors matches that search.</p>'}
              ${data.professors
                .map(
                  (item) => `
              <li class="professor-card__list-item">
              <a class="professor-card" href="${item.permalink}">
                  <img class="professor-card__image" src="${item.image}" alt="">
                  <span class="professor-card__name">
                    ${item.title}
                  </span>
              </a>
          </li>
              `
                )
                .join('')}
              ${data.professors.length ? '</ul>' : ''}
 

            </div>
            <div class="one-third">
              <h2 class="search-overlay__section-title">Events</h2>
              ${data.events.length ? '<ul class="link-list min-list">' : '<p>No events matches that search.</p>'}
              ${data.events
                .map(
                  (item) => `
                  <div class="event-summary">
    <a class="event-summary__date event-summary__date t-center" href="${item.permalink}">
        <span class="event-summary__month">
          ${item.month}
        </span>
        <span class="event-summary__day">
        ${item.day}

        </span>
    </a>

    <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">
              ${item.title}
            </a>
        </h5>
        <p>
            ${item.description}
            <a href="${item.permalink}" class="nu gray">Learn more</a>
        </p>

    </div>

</div>
              
              
              
              
              `
                )
                .join('')}
              ${data.events.length ? '</ul>' : ''}
 
            </div>
        </div>
        `;
    }).catch(() => {
      this.resultsDiv.innerHTML = '<p>Unexpected error; please try again.</p>';
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
