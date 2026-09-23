# stackAlert.js 🚀

**Plugin jQuery standalone per alert impilati con Bootstrap 5 e Font Awesome.**

stackAlert è un semplice plugin che consente di mostrare messaggi di notifica impilati, con posizionamento fisso nella pagina, chiusura automatica o manuale e un look moderno con animazioni ed icone. Tutto incluso in un solo file `.js`, senza necessità di caricare CSS separati.

---

## ✨ Caratteristiche

- ✅ Compatibile con **jQuery 3+** e **Bootstrap 5**
- ✅ Include automaticamente gli **stili CSS "pro"** al primo utilizzo
- ✅ Supporta **Font Awesome** per icone visive
- ✅ Posizionamento configurabile: `top-right`, `bottom-right`, `top-left`, `bottom-left`, `top-center`, `bottom-center`
- ✅ Chiusura automatica configurabile, oppure solo manuale
- ✅ Stack verticale degli alert (non sovrapposti)
- ✅ Design responsive e leggero

---

## 📦 Includere nel proprio progetto

Assicurati di includere jQuery, Bootstrap 5 e Font Awesome prima del plugin:

```html
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- stackAlert Plugin -->
<script src="https://cdn.jsdelivr.net/gh/FVLogika/stackAlert/stackAlert.min.js"></script>
```

---

## 🚀 Utilizzo base

```javascript
$.fn.stackAlert({
  message: 'Operazione completata con successo!',
  type: 'success',
  timeout: 4000,
  position: 'top-right'
});
// oppure
$.fn.stackAlert('Operazione completata con successo!', 'success', 4000, 'top-right');
```

---

## 🛑 Solo chiusura manuale (senza timeout)

```javascript
$.fn.stackAlert({
  message: 'Errore irreversibile!',
  type: 'danger',
  timeout: 0, // senza chiusura a tempo
  position: 'bottom-left'
});
// oppure
$.fn.stackAlert('Errore irreversibile!', 'danger', 0, 'bottom-left');
```

---

## 🎨 Tipologie supportate

| Tipo          | Colore     | Icona Font Awesome          |
| ------------- | ---------- | --------------------------- |
| `info`      | blu chiaro | `fa-circle-info`          |
| `success`   | verde      | `fa-circle-check`         |
| `warning`   | arancione  | `fa-triangle-exclamation` |
| `danger`    | rosso      | `fa-circle-exclamation`   |
| `primary`   | blu        | `fa-star`                 |
| `secondary` | grigio     | `fa-circle`               |
| `light`     | gr chiaro  | `fa-sun`                  |
| `dark`      | gr scuro   | `fa-moon`                 |

---

## 📁 Struttura del file

Tutto il codice è contenuto in un solo file JavaScript (`stackAlert.js`) che:

- Se necessario inietta lo stile automaticamente nel `<head>` della pagina
- Gestisce la creazione del contenitore e degli alert
- Applica le animazioni, le icone e il comportamento di chiusura
- È completamente standalone e non richiede dipendenze esterne oltre a jQuery, Bootstrap e Font Awesome

---

## 🧪 Esempi avanzati

### 🔄 Più alert impilati uno sopra l’altro

```javascript
$.fn.stackAlert({
  message: 'Primo messaggio!',
  type: 'info',
  timeout: 3000
});

$.fn.stackAlert({
  message: 'Secondo messaggio!',
  type: 'success',
  timeout: 5000
});

$.fn.stackAlert({
  message: 'Terzo alert!',
  type: 'warning',
  timeout: 7000
});

// oppure

$.fn.stackAlert('Primo messaggio!', 'info', 3000);
$.fn.stackAlert('Secondo messaggio!', 'success', 5000);
$.fn.stackAlert('Terzo alert!', 'warning', 7000);
```

### 🧭 Posizionamento custom

```javascript
$.fn.stackAlert({
  message: 'Notifica in basso a destra',
  position: 'bottom-right'
});
// oppure
$.fn.stackAlert('Notifica in basso a destra', 'info', 5000, 'bottom-right');
```

---

## 📌 Suggerimenti

- Puoi usare questo plugin per messaggi di conferma, errori, notifiche temporanee o avvisi persistenti
- Essendo basato su Bootstrap, puoi facilmente personalizzare il look usando le classi di Bootstrap (`alert-*`)
- Il plugin può essere facilmente esteso per supportare icone personalizzate, callback o stacking orizzontale

---

## 📄 Licenza

Questo progetto è open-source e liberamente utilizzabile.

---

## 🛠 Autore

Creato con cura da **[FVLogika](https://github.com/FVLogika)**

---
