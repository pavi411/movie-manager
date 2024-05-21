class ScrollableDiv {
  constructor(div) {
    this.div = div;
    this.leftPaddle = div.querySelector('.left-scrollbar');
    this.rightPaddle = div.querySelector('.right-scrollbar');
    this.scrollMenu = div.querySelector('.scroll-items');
    this.items = this.scrollMenu.querySelectorAll('.scroll-item');

    this.paddleMargin = 20;

    this.leftPaddle.classList.add('hidden');

    let self = this;

    this.scrollMenu.onscroll = function() {
      let menuPosition = self.scrollMenu.scrollLeft;
      let menuInvisibleSize = self.scrollMenu.scrollWidth - self.div.offsetWidth;
      let menuEndOffset = menuInvisibleSize - self.paddleMargin;
      if(menuPosition < self.paddleMargin) {
        self.leftPaddle.classList.add('hidden');
        self.rightPaddle.classList.remove('hidden');
      } else if (menuPosition < menuEndOffset) {
        self.leftPaddle.classList.remove('hidden');
        self.rightPaddle.classList.remove('hidden');
      } else {
        self.leftPaddle.classList.remove('hidden');
        self.rightPaddle.classList.add('hidden');
      }
    }

    this.leftPaddle.onclick = function() {
      self.scrollMenu.scrollLeft -= Math.round(self.scrollMenu.scrollWidth/self.items.length);
    }

    this.rightPaddle.onclick = function() {
      self.scrollMenu.scrollLeft += Math.round(self.scrollMenu.scrollWidth/self.items.length);
    }
  }
}
