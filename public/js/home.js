   // Fade-in banner saat load
      window.addEventListener('load', () => {
        document.getElementById('banner-content').classList.add('show');
      });
      
      // Efek scroll ke "About"
      document.getElementById('lihat-btn').addEventListener('click', () => {
        document.getElementById('about').scrollIntoView({ behavior: 'smooth' });
      });
      
      // Animasi muncul kartu saat discroll
      const cards = document.querySelectorAll('.card');
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('show');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });
      
      cards.forEach(card => observer.observe(card));
      
      // Tahun otomatis
      document.getElementById('year').textContent = new Date().getFullYear();
      const banner = document.querySelector('.banner');
const images = [];

images.push(banner.dataset.img1);
images.push(banner.dataset.img2);
images.push(banner.dataset.img3);

let index = 0;

banner.style.backgroundImage = `url('${images[index]}')`;

setInterval(() => {
  index = (index + 1) % images.length;
  banner.style.backgroundImage = `url('${images[index]}')`;
}, 5000);
