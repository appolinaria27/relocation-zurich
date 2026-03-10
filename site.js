const translations = {
  en: {
    nav_home: "Home",
    nav_about: "About",
    nav_services: "Services",
    nav_blog: "Blog",
    nav_contact: "Contact",
    nav_book: "Pay / Book",

    hero_kicker: "Zürich • Online / In-person",
    hero_title: "Clear support for settling in Switzerland",
    hero_text: "Visas, Anmeldung, registrations, documents and practical legal guidance — for clients from Latin America, Eastern Europe, and worldwide.",

    hero_ed: "6+",
    hero_ed2: "years legal education",

    hero_cons: "24+",
    hero_cons2: "consultations completed",

    hero_exp: "5+",
    hero_exp2: "years migrant experience",

    hero_lang: "5",
    hero_lang2: "languages"
  },

  es: {
    nav_home: "Inicio",
    nav_about: "Sobre mí",
    nav_services: "Servicios",
    nav_blog: "Blog",
    nav_contact: "Contacto",
    nav_book: "Pagar / Reservar",

    hero_kicker: "Zúrich • Online / Presencial",
    hero_title: "Apoyo claro para establecerse en Suiza",
    hero_text: "Visas, Anmeldung, registros, documentos y asesoría legal práctica para clientes de América Latina, Europa del Este y de todo el mundo.",

    hero_ed: "6+",
    hero_ed2: "años de educación jurídica",

    hero_cons: "24+",
    hero_cons2: "consultas realizadas",

    hero_exp: "5+",
    hero_exp2: "años de experiencia migratoria",

    hero_lang: "5",
    hero_lang2: "idiomas"
  },

  de: {
    nav_home: "Startseite",
    nav_about: "Über mich",
    nav_services: "Leistungen",
    nav_blog: "Blog",
    nav_contact: "Kontakt",
    nav_book: "Bezahlen / Buchen",

    hero_kicker: "Zürich • Online / Persönlich",
    hero_title: "Klare Unterstützung beim Leben in der Schweiz",
    hero_text: "Visa, Anmeldung, Registrierungen, Dokumente und praktische rechtliche Beratung für Klienten aus Lateinamerika, Osteuropa und weltweit.",

    hero_ed: "6+",
    hero_ed2: "Jahre juristische Ausbildung",

    hero_cons: "24+",
    hero_cons2: "durchgeführte Beratungen",

    hero_exp: "5+",
    hero_exp2: "Jahre Migrationserfahrung",

    hero_lang: "5",
    hero_lang2: "Sprachen"
  },

  uk: {
    nav_home: "Головна",
    nav_about: "Про мене",
    nav_services: "Послуги",
    nav_blog: "Блог",
    nav_contact: "Контакти",
    nav_book: "Оплата / Бронювання",

    hero_kicker: "Цюрих • Онлайн / Особисто",
    hero_title: "Чітка підтримка для життя у Швейцарії",
    hero_text: "Візи, Anmeldung, реєстрації, документи та практична юридична допомога для клієнтів з Латинської Америки, Східної Європи та з усього світу.",

    hero_ed: "6+",
    hero_ed2: "років юридичної освіти",

    hero_cons: "24+",
    hero_cons2: "проведених консультацій",

    hero_exp: "5+",
    hero_exp2: "років міграційного досвіду",

    hero_lang: "5",
    hero_lang2: "мов"
  }
};

function setLanguage(lang) {
  localStorage.setItem("siteLanguage", lang);

  document.querySelectorAll("[data-i18n]").forEach((el) => {
    const key = el.dataset.i18n;

    if (translations[lang] && translations[lang][key]) {
      el.textContent = translations[lang][key];
    } else {
      console.log("Missing translation for:", lang, key);
    }
  });

  document.querySelectorAll(".lang-toggle").forEach((btn) => {
    if (lang === "en") btn.textContent = "English ▾";
    if (lang === "es") btn.textContent = "Español ▾";
    if (lang === "de") btn.textContent = "Deutsch ▾";
    if (lang === "uk") btn.textContent = "Українська ▾";
  });

  document.querySelectorAll(".lang-menu").forEach((menu) => {
    menu.classList.remove("show");
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const saved = localStorage.getItem("siteLanguage") || "en";
  setLanguage(saved);

  document.querySelectorAll(".lang-toggle").forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      const menu = this.nextElementSibling;
      if (!menu) return;

      document.querySelectorAll(".lang-menu").forEach((otherMenu) => {
        if (otherMenu !== menu) {
          otherMenu.classList.remove("show");
        }
      });

      menu.classList.toggle("show");
    });
  });

  document.querySelectorAll(".lang-menu button").forEach((button) => {
    button.addEventListener("click", function () {
      const lang = this.getAttribute("data-lang");
      if (lang) setLanguage(lang);
    });
  });

  document.addEventListener("click", function (e) {
    document.querySelectorAll(".lang-dropdown").forEach((dropdown) => {
      if (!dropdown.contains(e.target)) {
        const menu = dropdown.querySelector(".lang-menu");
        if (menu) menu.classList.remove("show");
      }
    });
  });
});