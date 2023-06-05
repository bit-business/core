export const label = "Hrvatski";

export default {
  button: {
    close: "Zatvori",
    submit: "Spremi",
  },
  vuelidate: {
    required: "* {0} je potrebno.",
    requiredIf: "* {0} je potrebno.",
    integer: "* {0} mora biti cijeli broj.",
    rowsRequired: "* Molimo popunite polje.",
    maxLength: "* {field} mora imati najviše {length} slova.",
    alphaNum: "* {0} nije alfanumerički.",
    alphaNumAndUnderscoreValidator:
      "* {0} samo alfanumerički i donja crta (_) su dozvoljeni.",
    unique: "* {0} mora biti jedinstveno.",
    distinct: "* Dozvoljen je samo jedan {0}.",
    requiredPrimary: "* Dozvoljen je samo {0}.",
  },
  login: {
    title: "Nadzorna ploča",
    subtitle: "Dobrodošli nazad, molimo prijavite se na svoj račun.",
    field: {
      email: "Email",
      password: "Lozinka",
    },
    rememberMe: "Zapamti me?",
    forgotPassword: "Zaboravljena lozinka",
    button: "Prijava",
    createAccount: {
      text: "Nemate račun?",
      link: "Kreirajte račun",
    },
  },

  register: {
    title: "Registrirajte se Ovdje",
    subtitle: "Molimo popunite formular ispod.",
    field: {
      name: "Ime",
      username: "Korisničko ime",
      phone: "Telefon",
      email: "Email",
      password: "Lozinka",
      passwordConfirmation: "Potvrda Lozinke",
    },
    button: "Registracija",
    existingAccount: {
      text: "Imate račun?",
      link: "Prijava",
    },
  },

  forgotPassword: {
    title: "Zaboravljena Lozinka",
    subtitle: "Molimo unesite email za slanje linka za resetiranje lozinke.",
    field: {
      email: "Email",
    },
    button: "Pošalji",
    createAccount: {
      text: "Nemate račun?",
      link: "Kreirajte račun",
    },
    message: {
      success:
        "Email je poslan na adresu koju ste naveli. Molimo slijedite link u emailu da završite vaš zahtjev za resetiranje lozinke.",
      error:
        "Došlo je do greške. Molimo provjerite ponovno email koji ste unijeli. Ako problem i dalje traje, molimo kontaktirajte nas za daljnju pomoć.",
    },
  },

  resetPassword: {
    title: "Resetiranje Lozinke",
    subtitle: "Molimo unesite novu lozinku.",
    field: {
      password: "Nova Lozinka",
      passwordConfirmation: "Potvrda Nove Lozinke",
    },
    button: "Resetiraj Lozinku",
    createAccount: {
      text: "Nemate račun?",
      link: "Kreirajte račun",
    },
    message: {
      success:
        "Resetiranje lozinke je uspješno. Sada se možete prijaviti sa novom lozinkom. Bit ćete preusmjereni na stranicu za prijavu.",
      error:
        "Došlo je do greške. Molimo provjerite ponovno lozinku i potvrdu lozinke koje ste unijeli. Ako problem i dalje traje, molimo kontaktirajte nas za daljnju pomoć.",
    },
  },

  verifyEmail: {
    title: "Verifikacija Emaila",
    failed: "Verifikacija emaila je u tijeku ....",
    button: "Verifikuj",
    request: "Pošalji ponovno",
    field: {
      token: "Token",
    },
    message: {
      inProgress: "Verifikacija emaila je u tijeku ....",
      success:
        "Email je poslan na adresu koju ste naveli. Molimo slijedite link u emailu da završite verifikaciju vašeg emaila.",
      error: "Verifikacija emaila nije uspjela.",
    },
  },

  sidebar: {
    dashboard: "Nadzorna ploča",
    mainMenu: "Glavni izbornik",
    configurationMenu: "Meni za Konfiguraciju",
  },

  myProfile: {
    title: "Moj Profil",
    username: "Korisničko ime",
    logout: "Odjava",
    profile: "Profil",
    email: "Email",
    password: "Lozinka",
    oldPassword: "Trenutna Lozinka",
    newPassword: "Nova Lozinka",
    newPasswordConfirmation: "Potvrda Nove Lozinke",
    name: "Ime",
    avatar: "Slika profila",
    additionalInfo: "Dodatne informacije(opcionalno)",
    token: "Kod za Verifikaciju",
    buttons: {
      updateProfile: "Ažuriraj Profil",
      updateEmail: "Ažuriraj Email",
      verifyEmail: "Verifikuj Email",
      changePassword: "Promijeni Lozinku",
    },
  },

  404: {
    title: "Ups, Oprostite",
    subtitle: "Stranica koju tražite nije pronađena.",
    button: "Idi na Početnu",
  },

  action: {
    bulkDelete: "Masovno Brisanje",
    bulkRestore: "Masovno Vraćanje",
    showTrash: "Prikaži Otpad",
    add: "Dodaj",
    edit: "Uredi",
    delete: {
      title: "Potvrdi",
      text: "Jeste li sigurni?",
      accept: "Prihvati",
      cancel: "Odustani",
    },
    restore: {
      title: "Potvrdi",
      text: "Jeste li sigurni da želite vratiti?",
      accept: "Prihvati",
      cancel: "Odustani",
    },
    addItem: "Dodaj Stavku",
    roles: "Uloge",
    sort: "Sortiraj",
    rollbackMigration: {
      title: "Poništi Migraciju",
      text: "Jeste li sigurni?",
      accept: "Prihvati",
      cancel: "Odustani",
    },
    exportToExcel: "Izvoz u .xls",
    exportToPdf: "Izvoz u .pdf",
  },

  alert: {
    success: "Uspješno",
    danger: "Opasnost",
    error: "Greška",
  },

  activityLog: {
    title: "Dnevnik Aktivnosti",
    warning: {
      notAllowed: "Nije vam dozvoljeno pregledavanje Dnevnika Aktivnosti.",
    },
    header: {
      logName: "Naziv Dnevnika",
      causerType: "Vrsta Korisnika",
      causerId: "Id Korisnika",
      causerName: "Ime Korisnika",
      subjectType: "Vrsta Subjecta",
      subjectId: "Id Subjecta",
      description: "Opis",
      dateLogged: "Datum Prijave",
      action: "Uredi",
    },
    footer: {
      descriptionTitle: "Registri",
      descriptionConnector: "od",
      descriptionBody: "Stranice",
    },
    detail: {
      title: "Detalji Dnevnika Aktivnosti",
      causer: {
        title: "Detalji Korisnika",
      },
      subject: {
        title: "Detalji Subjecta",
      },
      properties: {
        title: "Svojstva",
      },
    },
  },

  site: {
    action: "Izmjene",
    deletedImage: {
      title: "Obrisana Slika",
      text: "Odabrana slika je uspješno obrisana",
    },
    configUpdated: "Konfiguracija Ažurirana",
    add: {
      title: "Dodaj Konfiguraciju Stranice",
      field: {
        displayName: {
          title: "Naziv za Prikaz",
          placeholder: "Primjer: Naziv za Prikaz",
        },
        key: {
          title: "Ključ",
          placeholder: "Primjer: ključ_primjer",
        },
        type: {
          title: "Tip",
          placeholder: "Tip",
        },
        group: {
          title: "Grupa",
          placeholder: "Grupa",
        },
        options: {
          title: "Opcije",
          description:
            "Opcije su potrebne za Checkbox, Radio, Select, Select-multiple. Primjer: ",
          example: `{"items": [{"label":"Ovo je oznaka","value":"ovo_je_vrijednost"}] }`,
        },
      },
      button: "Spremi",
    },
    edit: {
      multiple: "Ažuriraj Konfiguracije",
    },
    maintenanceMode: "Postavka održavanja je samo za čitanje."
  },

  crud: {
    title: "CRUD",
    data: {
      switchDataRecycle: "Prikaži Reciklirane Podatke",
      switchDataNormal: "Prikaži Normalne Podatke",
    },
    help: {
      generatePermissions:
        "Generirat će se dozvola za stvoreni CRUD. Generirane dozvole su: browse_{table_name}, read_{table_name}, edit_{table_name}, add_{table_name}, delete_{table_name} i maintenance_{table_name}.",
      serverSide:
        "Postavite straničenje na pregledavanje na server stranu ili stranu klijenta. Ako imate malo podataka, samo ovo postavite na Off, i obratno.",
      createSoftDelete:
        "Prebacite ovo na On ako želite značajku poput kante za otpatke. Možete oporaviti izbrisane podatke. Stvorit će se soft delete ako je tablica podržana.",
      activeEventNotificationTitle:
        "Ovo će pokazati obavijest na desnoj bočnoj traci ako je postavljeni događaj akcije. Prije korištenja ove značajke konfigurirajte push obavijesti za firebase.",
      modelName:
        "Popunite ovaj unos ako želite zamijeniti Model CRUD-a. Na primjer: App\\Models\\User.",
      controllerName:
        "Popunite ovaj unos ako želite zamijeniti Controller CRUD-a. Na primjer: App\\Http\\Controller\\HomeController. Možete zamijeniti jednu od sljedećih metoda: browse, all, read, edit, add, delete, restore, deleteMultiple, restoreMultiple, sort ili setMaintenanceState.",
    },
    warning: {
      notAllowed: "Nije vam dozvoljeno pregledavanje CRUD-a.",
      idNotAllowed: "Nemojte mijenjati ime id za bilo što",
    },
    header: {
      table: "Tablica",
      action: "Akcija",
    },
    body: {
      button: "Dodaj CRUD za ovu tablicu",
    },
    footer: {
      descriptionTitle: "Registri",
      descriptionConnector: "od",
      descriptionBody: "Stranice",
    },
    add: {
      title: {
        table: "Dodaj CRUD za {tableName}",
        field: "Dodaj Polja CRUD-a za {tableName}",
        advance: "Napredno Postavljanje",
      },
      field: {
        tableName: {
          title: "Ime Tablice",
          placeholder: "Ime Tablice",
        },
        generatePermissions: "Generiraj Dozvole",
        createSoftDelete: "Stvori Soft Delete",
        createSoftDeleteNote:
          "Napomena: ako stvorite soft delete, automatski smo stvorili model i migraciju za soft delete",
        serverSide: "Server Strana",
        displayNameSingular: {
          title: "Prikaz Imena (Jednina)",
          placeholder: "Prikaz Imena (Jednina)",
        },
        displayNamePlural: {
          title: "Prikaz Imena (Množina)",
          placeholder: "Prikaz Imena (Množina)",
        },
        urlSlug: {
          title: "URL Slug (mora biti jedinstven)",
          placeholder: "URL Slug (mora biti jedinstven)",
        },
        icon: {
          title: "Ikona",
          placeholder: "Ikona",
        },
        modelName: {
          title: "Ime Modela",
          placeholder: "Ime Modela",
        },
        controllerName: {
          title: "Ime Kontrolera",
          placeholder: "Ime Kontrolera",
        },
        orderColumn: {
          title: "Redoslijed Kolone",
          placeholder: "Redoslijed Kolone",
        },
        orderDisplayColumn: {
          title: "Redoslijed Prikazane Kolone",
          placeholder: "Redoslijed Prikazane Kolone",
          description:
            "<p class='text-muted'>Redoslijed Kolone će biti ispunjen brojevima za sortiranje podataka ako je ovo polje postavljeno</p>",
        },
        orderDisplayMoreColumn: {
          title: "Više Podataka Za API",
          placeholder: "Više Podataka Za API",
          description:
            "<p class='text-muted'>Redoslijed Kolone će biti ispunjen brojevima za sortiranje podataka ako je ovo polje postavljeno</p>",
        },
        orderDirection: {
          title: "Redoslijed Smjera",
          value: {
            ascending: "Uzlazno",
            descending: "Silazno",
          },
          placeholder: "Redoslijed Smjera",
        },
        defaultServerSideSearchField: {
          title: "Zadano Polje za Pretragu na Server Strani",
          placeholder: "Zadano Polje za Pretragu na Server Strani",
        },
        description: {
          title: "Opis",
          placeholder: "Opis",
        },
      },
      header: {
        field: "Polje",
        visibility: "Vidljivost",
        inputType: "Tip Unosa",
        displayName: "Prikaz Imena",
        optionalDetails: "Opcionalni Detalji",
      },
      body: {
        type: "Vrsta:",
        required: {
          title: "Obavezno:",
          yes: "Da",
          no: "Ne",
        },
        browse: "Pregledaj",
        read: "Čitaj",
        edit: "Izmijeni",
        add: "Dodaj",
        delete: "Obriši",
        displayName: "Ime za prikaz",
        setRelation: "Postavi relaciju",
        setOtherRelation: "Postavi drugu relaciju",
        relationType: "Vrsta relacije",
        destinationTable: "Odredišna tabela",
        destinationTableManytomany: "Odredišna tabela Mnogo-na-Mnogo",
        destinationTableColumn: "Odredišna kolona",
        destinationTableDisplayColumn: "Kolona za prikaz",
        destinationTableDisplayMoreColumn: "Dodatni podaci za API",
        saveRelation: "Spremi",
        cancelRelation: "Otkaži",
      },
      button: "Spremi",
    },
    edit: {
      title: {
        table: "Izmijeni CRUD za {tableName}",
        field: "Izmijeni CRUD polja za {tableName}",
        advance: "Napredne postavke",
      },
      field: {
        tableName: {
          title: "Ime tabele",
          placeholder: "Ime tabele",
        },
        generatePermissions: "Generiši dozvole",
        serverSide: "Server strana",
        displayNameSingular: {
          title: "Ime za prikaz (jednina)",
          placeholder: "Ime za prikaz (jednina)",
        },
        displayNamePlural: {
          title: "Ime za prikaz (množina)",
          placeholder: "Ime za prikaz (množina)",
        },
        urlSlug: {
          title: "URL Slug (mora biti jedinstven)(samo za čitanje)",
          placeholder: "URL Slug (mora biti jedinstven)",
        },
        icon: {
          title: "Ikona",
          placeholder: "Ikona",
        },
        modelName: {
          title: "Ime modela",
          placeholder: "Ime modela",
        },
        controllerName: {
          title: "Ime kontrolera",
          placeholder: "Ime kontrolera",
        },
        orderColumn: {
          title: "Kolona za sortiranje",
          placeholder: "Kolona za sortiranje",
        },
        orderDisplayColumn: {
          title: "Kolona za prikaz sortiranja",
          placeholder: "Kolona za prikaz sortiranja",
          description:
            "<p class='text-muted'>Kolona za sortiranje će biti popunjena brojevima za sortiranje podataka ako je ovo polje postavljeno</p>",
        },
        orderDisplayMoreColumn: {
          title: "Dodatni podaci za API",
          placeholder: "Dodatni podaci za API",
          description:
            "<p class='text-muted'>Kolona za sortiranje će biti popunjena brojevima za sortiranje podataka ako je ovo polje postavljeno</p>",
        },
        orderDirection: {
          title: "Smjer sortiranja",
          value: {
            ascending: "Uzlazno",
            descending: "Silazno",
          },
          placeholder: "Smjer sortiranja",
        },
        defaultServerSideSearchField: {
          title: "Podrazumevano polje za pretragu na serveru",
          placeholder: "Podrazumevano polje za pretragu na serveru",
        },
        description: {
          title: "Opis",
          placeholder: "Opis",
        },
        activeEventNotification: {
          title: "Aktivne obavijesti o događaju",
          label: {
            onCreate: "Pri kreiranju",
            onRead: "Pri čitanju",
            onUpdate: "Pri ažuriranju",
            onDelete: "Pri brisanju",
            onCreateTitle: "Naslov poruke događaja pri kreiranju",
            onCreateMessage: " Poruka događaja pri kreiranju",
            onReadTitle: "Naslov poruke događaja pri čitanju",
            onReadMessage: " Poruka događaja pri čitanju",
            onUpdateTitle: "Naslov poruke događaja pri ažuriranju",
            onUpdateMessage: " Poruka događaja pri ažuriranju",
            onDeleteTitle: "Naslov poruke događaja pri brisanju",
            onDeleteMessage: " Poruka događaja pri brisanju",
          },
        },
      },
      header: {
        field: "Polje",
        visibility: "Vidljivost",
        inputType: "Vrsta unosa",
        displayName: "Ime za prikaz",
        optionalDetails: "Opcioni detalji",
      },
      body: {
        type: "Vrsta:",
        required: {
          title: "Obavezno:",
          yes: "Da",
          no: "Ne",
        },
        browse: "Pregledaj",
        read: "Čitaj",
        edit: "Uredi",
        add: "Dodaj",
        delete: "Obriši",
        displayName: "Ime za prikaz",
        setRelation: "Postavi vezu",
        setOtherRelation: "Postavi drugu vezu",
        relationType: "Vrsta veze",
        destinationTable: "Odredišna tabela",
        destinationTableManytomany: "Odredišna tabela 'Mnogo na mnogo'",
        destinationTableColumn: "Odredišna kolona",
        destinationTableDisplayColumn: "Kolona za prikaz",
        destinationTableDisplayMoreColumn: "Dodatni podaci za API",
        saveRelation: "Spremi",
        cancelRelation: "Otkaži",
      },
      button: "Spremi",
    },
  },

  menu: {
    title: "Meni",
    options: {
      showHeader: "Pokaži meni zaglavlja",
      expand: "Proširi",
    },
    warning: {
      notAllowedToBrowse: "Nije vam dozvoljeno da pregledate meni",
      notAllowedToAdd: "Nije vam dozvoljeno da dodate meni",
      notAllowedToEdit: "Nije vam dozvoljeno da uređujete meni",
    },
    help: {
      key: "Možete postaviti ovaj ključ da bude podrazumevani meni u .env fajlu. Takođe, možete registrovati novi meni u .env fajlu unosom vrednosti.",
    },
    header: {
      key: "Ključ",
      displayName: "Ime za prikaz",
      action: "Akcija",
    },
    footer: {
      descriptionTitle: "Registri",
      descriptionConnector: "od",
      descriptionBody: "Stranice",
    },
    add: {
      title: "Dodaj meni",
      field: {
        key: {
          title: "Ključ",
          placeholder: "kljuc_menija",
        },
        displayName: {
          title: "Ime za prikaz",
          placeholder: "Ime za prikaz",
        },
        icon: {
          title: "Ikona",
          placeholder: "Ikona",
        },
      },
      button: "Spremi",
    },
    edit: {
      title: "Uredi meni",
      field: {
        key: {
          title: "Ključ",
          placeholder: "kljuc_menija",
        },
        displayName: {
          title: "Ime za prikaz",
          placeholder: "Ime za prikaz",
        },
      },
      button: "Spremi",
    },
    builder: {
      title: "Stavka menija",
      popup: {
        add: {
          title: "Dodaj stavku menija",
          field: {
            title: "Naslov",
            url: "Url",
            target: {
              title: "Cilj",
              value: {
                thisTab: "Ova kartica",
                newTab: "Nova kartica",
              },
            },
            icon: {
              title: "Ikona",
              description:
                "Koristite&nbsp;<a href='https://material.io/resources/icons/?style=baseline' target='_blank'>material design ikone</a>",
            },
          },
          button: {
            add: "Dodaj",
            cancel: "Otkaži",
          },
        },
        edit: {
          title: "Uredi stavku menija",
          field: {
            title: "Naslov",
            url: "Url",
            target: {
              title: "Cilj",
              value: {
                thisTab: "Ova kartica",
                newTab: "Nova kartica",
              },
            },
            icon: {
              title: "Ikona",
              description:
                "Koristite&nbsp;<a href='https://material.io/resources/icons/?style=baseline' target='_blank'>material design ikone</a>",
            },
          },
          button: {
            edit: "Uredi",
            cancel: "Otkaži",
          },
        },
      },
    },
    permission: {
      title: "Dozvole",
      header: {
        key: "Ključ",
        description: "Opis",
      },
      button: "Postavi izabrane dozvole za meni",
      success: {
        title: "Uspješno",
        text: "Dozvole su postavljene",
      },
    },
  },

  user: {
    title: "Korisnik",
    header: {
      name: "Ime",
      email: "E-pošta",
      action: "Akcija",
    },
    footer: {
      descriptionTitle: "Registri",
      descriptionConnector: "od",
      descriptionBody: "Strane",
    },
    help: {
      emailVerified:
        "Prebacite ovo na uključeno da bi automatski verifikovali e-poštu kreiranog korisnika",
    },
    add: {
      title: "Dodaj korisnika",
      field: {
        name: {
          title: "Ime",
          placeholder: "Ime",
        },
        username: {
          title: "Korisničko ime",
          placeholder: "Korisničko ime",
        },
        email: {
          title: "E-pošta",
          placeholder: "E-pošta",
        },
        password: {
          title: "Lozinka",
          placeholder: "Lozinka",
        },
        emailVerified: {
          title: "Da li je e-pošta verifikovana",
          placeholder: "Da li je e-pošta verifikovana",
        },
        avatar: {
          title: "Slika profila",
          placeholder: "Slika profila",
        },
        additionalInfo: {
          title: "Dodatne informacije",
          placeholder: "Dodatne informacije",
          invalid: "Dodatne informacije su nevažeće",
        },
      },
      button: "Spremi",
    },
    edit: {
      title: "Izmjeni korisnika",
      field: {
        name: {
          title: "Ime",
          placeholder: "Ime",
        },
        username: {
          title: "Korisničko ime",
          placeholder: "Korisničko ime",
        },
        email: {
          title: "E-pošta",
          placeholder: "E-pošta",
        },
        password: {
          title: "Lozinka",
          placeholder: "Ostavi prazno ako se ne mijenja",
        },
        emailVerified: {
          title: "Da li je e-pošta verificirana",
          placeholder: "Da li je e-pošta verificirana",
        },
        avatar: {
          title: "Slika porfila",
          placeholder: "Nova slika",
        },
        additionalInfo: {
          title: "Dodatne informacije",
          placeholder: "Dodatne informacije",
          invalid: "Dodatne informacije su nevažeće",
        },
      },
      button: "Spremi",
    },
    detail: {
      title: "Detalji korisnika",
      avatar: "Avatar",
      name: "Ime",
      username: "Korisničko ime",
      email: "E-pošta",
      additionalInfo: "Dodatne informacije",
      emailVerified: "Da li je e-pošta verificirana",
    },
    roles: {
      title: "Uloge",
      header: {
        name: "Ime",
        description: "Opis",
        action: "Akcija",
      },
      button: "Postavi izabrane uloge za korisnika",
      success: {
        title: "Uspješno",
        text: "Uloge su postavljene",
      },
    },
  },

  role: {
    title: "Uloga",
    warning: {
      notAllowedToBrowse: "Nije vam dozvoljeno da pregledate ulogu",
      notAllowedToAdd: "Nije vam dozvoljeno da dodate ulogu",
      notAllowedToEdit: "Nije vam dozvoljeno da izmenite ulogu",
      notAllowedToBrowsePermission:
        "Nije vam dozvoljeno da pregledate dozvole uloge",
    },
    header: {
      name: "Ime",
      displayName: "Prikazano ime",
      description: "Opis",
      action: "Akcija",
    },
    footer: {
      descriptionTitle: "Registri",
      descriptionConnector: "od",
      descriptionBody: "Stranice",
    },
    add: {
      title: "Dodaj ulogu",
      field: {
        name: {
          title: "Ime",
          placeholder: "Ime",
        },
        displayName: {
          title: "Prikazano ime",
          placeholder: "Prikazano ime",
        },
        description: {
          title: "Opis",
          placeholder: "Opis",
        },
      },
      button: "Sačuvaj",
    },
    edit: {
      title: "Izmjeni ulogu",
      field: {
        name: {
          title: "Ime",
          placeholder: "Ime",
        },
        displayName: {
          title: "Prikazano ime",
          placeholder: "Prikazano ime",
        },
        description: {
          title: "Opis",
          placeholder: "Opis",
        },
      },
      button: "Sačuvaj",
    },
    detail: {
      title: "Detalji uloge",
      name: "Ime",
      displayName: "Prikazano ime",
      description: "Opis",
      button: {
        edit: "Izmijeni",
        permission: "Dozvola",
      },
    },
    permission: {
      title: "Dozvole",
      header: {
        key: "Ključ",
        description: "Opis",
      },
      button: "Postavi izabrane dozvole za ulogu",
      success: {
        title: "Uspješno",
        text: "Dozvole su postavljene",
      },
    },
  },

 permission: {
    title: "Dozvola",
    warning: {
      notAllowedToBrowse: "Nemate dozvolu da pregledate Dozvolu",
      notAllowedToAdd: "Nemate dozvolu da dodate Dozvolu",
      notAllowedToEdit: "Nemate dozvolu da izmjenite Dozvolu",
      notAllowedToRead: "Nemate dozvolu da pročitate Dozvolu",
    },
    help: {
      alwaysAllow:
        "Nakon što je dozvola kreirana, dodjeliće se svakoj ulozi koja je kreirana nakon dozvole",
      isPublic: "Dozvole će biti javno dostupne",
    },
    header: {
      key: "Ključ",
      description: "Opis",
      tableName: "Ime tabele",
      alwaysAllow: "Uvijek dozvoli",
      isPublic: "Javno",
      action: "Akcija",
      rolesCanSeeAllData: "Uloge mogu vidjeti sve podatke",
      fieldIdentifyRelatedUser: "Polje Identificiraju Povezanog Korisnika"
    },
    footer: {
      descriptionTitle: "Registri",
      descriptionConnector: "od",
      descriptionBody: "Stranice",
    },
    add: {
      title: "Dodaj Dozvolu",
      field: {
        key: {
          title: "Ključ",
          placeholder: "Ključ",
        },
        alwaysAllow: "Uvijek dozvoli",
        isPublic: "Javno",
        description: {
          title: "Opis",
          placeholder: "Opis",
        },
        tableName: {
          title: "Ime tabele",
          placeholder: "Ime tabele",
        },
      },
      button: "Spremi",
    },
    edit: {
      title: "Izmijeni Dozvolu",
      field: {
        key: {
          title: "Ključ",
          placeholder: "Ključ",
        },
        alwaysAllow: "Uvijek dozvoli",
        isPublic: "Javno",
        description: {
          title: "Opis",
          placeholder: "",
        },
        tableName: {
          title: "Ime tabele",
          placeholder: "Ime tabele",
        },
        rolesCanSeeAllData: {
          title: "Uloga može vidjeti sve podatke",
          placeholder: "Uloga može vidjeti sve podatke",
        },
        fieldIdentifyRelatedUser: {
          title: "Kolona za identifikaciju povezanih korisničkih podataka",
          placeholder: "Kolona za identifikaciju povezanih korisničkih podataka",
        }
      },
      button: "Spremi",
    },

    detail: {
      title: "Detail Permission",
      key: "Key",
      description: "Description",
      tableName: "Table Name",
      alwaysAllow: {
        title: "Always Allow",
        yes: "Yes",
        no: "No",
      },
      isPublic: {
        title: "Is Public",
        yes: "Yes",
        no: "No",
      },
      button: "Edit",
      rolesCanSeeAllData: "Roles Can See All Data",
      fieldIdentifyRelatedUser: "Field Identify Related User"
    },
  },

  crudGenerated: {
    warning: {
      notAllowedToBrowse: "You're not allowed to browse {tableName}",
      notAllowedToAdd: "You're not allowed to add {tableName}",
      notAllowedToEdit: "You're not allowed to edit {tableName}",
      notAllowedToRead: "You're not allowed to read {tableName}",
    },
    header: {
      action: "Action",
    },
    footer: {
      descriptionTitle: "Registries",
      descriptionConnector: "of",
      descriptionBody: "Pages",
    },
    add: {
      title: "Add {tableName}",
      button: "Save",
    },
    edit: {
      title: "Edit {tableName}",
      button: "Update",
    },
    detail: {
      title: "Detail {tableName}",
      button: "Edit",
    },
    sort: {
      title: "Sort {tableName}",
    },
    maintenanceDialog: {
      title: "Setting",
      switch: "Maintenance Mode",
      button: "Save",
    },
  },
  keyIssue: {
    title: "License Issues",
    message:
      "Sorry, Skijasi cannot be used because there is an issue on your license",
    listTitle: "Here are some of the problems that can occur with a license:",
    licenseEmpty: "License Empty",
    licenseEmptyDescription:
      "You haven't entered SKIJASI_LICENSE_KEY in .env. Please fill in before you can use Skijasi. For more complete instructions, please see here.",
    licenseInvalid: "License Invalid",
    licenseInvalidDescription:
      "SKIJASI_LICENSE_KEY was not found. Please make sure it is the same as what you get on Skijasi Dashboard. For more complete instructions, please see here.",
    licenseUsersExpired: "Active Period Expires",
    licenseUsersExpiredDescription:
      "Your active period has expired. Please add your active period to Skijasi Dashboard so that your license can be used again. For more complete instructions, please see here.",
  },
  authorizationIssue: {
    title: "Session Expired",
    subtitle: "Sorry, cannot continue request because",
    message: "Authorization Failed, token expired or empty",
  },
  database: {
    browse: {
      title: "Database",
      addButton: "Add Table",
      alterButton: "Alter Table",
      rollbackButton: "Rollback Migration",
      dropButton: "Drop Table",
      goBackButton: "Go Back",
      deleteMigrationButton: "Delete Migration File",
      migrateButton: "Migrate",
      warning: {
        title: "Migration Not Sync",
        notAllowed:
          "Before you can use the Database Management, you should migrate the file that not migrated yet or you could delete the migration file. Here is a list of the migration files that haven't been migrated:",
        empty: "You must delete this generated CRUD first on CRUD Management.",
      },
      fieldNotSupport: {
        title: "Database Error",
        text: "There's unsupported data type in table, please see the supported data type in skijasi documentation",
        tableList: "List of unsupported tables :",
        button: {
          visitDocs: "Visit Documentation",
        },
      },
    },
    add: {
      title: "Add Table",
      field: {
        table: "Table Name",
      },
      row: {
        title: "Add Table Field",
        subtitle: "Please read the {0} before you create the migration.",
        field: {
          timestamp: "Timestamp",
          tableName: "Table Name",
          fieldName: "Field Name",
          fieldType: "Field Type",
          fieldLength: "Length/Value",
          fieldDefault: "Default",
          asDefined: "User Defined Default Value",
          fieldNull: "Nullable",
          fieldIndex: "Index",
          fieldAttribute: "Unsigned",
          fieldIncrement: "Auto Increment",
          action: "Action",
          add: "Add",
        },
      },
      error: {
        fieldName: "Field name is required.",
        fieldType: "Field type is required.",
        tableName: "Table name is required.",
        fieldLength: "Field length is required.",
      },
      footer: {
        descriptionTitle: "Registries",
        descriptionConnector: "of",
        descriptionBody: "Pages",
      },
      button: "Save",
    },
    edit: {
      title: "Alter Table",
      field: {
        table: "Table Name",
      },
      row: {
        title: "Alter Table Field",
        field: {
          timestamp: "Timestamp",
          tableName: "Table Name",
          fieldName: "Field Name",
          fieldType: "Field Type",
          fieldLength: "Length",
          fieldDefault: "Default",
          asDefined: "User Defined Default Value",
          fieldNull: "Nullable",
          fieldIndex: "Index",
          fieldAttribute: "Unsigned",
          fieldIncrement: "Auto Increment",
          action: "Action",
          add: "Add",
        },
        drop: "Are you sure want to delete this field?",
      },
      warning: {
        title: "IMPORTANT",
        content:
          'Only the following column types can be "changed": Big Integer, BLOB, Boolean, Date, Datetime, Decimal, Float, Integer, JSON, Long Text, Medium Text, Set, Small Integer, Varchar, Text and Time. Also, every field that you change, it\'ll be recorded when you submit the alter table. If you make some mistakes, you can refresh this page to reset your changes.',
        crud: "Make sure the table has not been generated with CRUD Management if you want to edit or drop it.",
        notAllowed: "You're not allowed to edit.",
        fieldAttUnsigned:
          "Foreign key constraint is incorrectly formed. {0} to visit docs.",
        visitDocs: "Click here",
      },
      error: {
        fieldName: "Field name is required.",
        fieldType: "Field type is required.",
        tableName: "Table name is required.",
        fieldLength: "Field length is required.",
      },
      footer: {
        descriptionTitle: "Registries",
        descriptionConnector: "of",
        descriptionBody: "Pages",
      },
      button: "Save",
    },
    rollback: {
      title: "Rollback",
      label: "Enter rollback step",
      checkbox: "Delete Migration File?",
      invalid: "Please select the migration that you want to rollback.",
    },
    warning: {
      docs: "* Please read the {0} before using this feature.",
      exists: "The {0} field already exists",
      invalid:
        "Request is invalid. Please check the fields or table name if it's valid or not.",
      empty: "Request is invalid. No changes were made.",
      errorOnRequest: "Request is invalid.",
    },
    migration: {
      header: {
        migration: "Migration Name",
      },
      button: {
        rollback: "Rollback Migration",
      },
    },
  },
  fileManager: {
    title: "Upravljač datotekama",
    warning: {
      notAllowedToBrowse: "Nemate dozvolu da pregledate upravljač datotekama",
    },
    URL: {
      label: "Nalepi URL slike ovde",
      placeholder: "URL",
      descriptionText:
        "Ako je vaš URL tačan, ovde ćete videti pregled slike. Velike slike mogu se pojaviti nakon nekoliko minuta. Prihvatamo samo PNG i JPEG.",
      invalid: "Slika nije važeća",
    },
  },
  imageManager: {
    title: "Upravljač slika",
    warning: {
      notAllowedToBrowse: "Nemate dozvolu da pregledate upravljač slika",
    },
  },
  firebase: {
    title: "Firebase",
    feature: "Funkcija",
    features: {
      firebaseCloudMessage: "Firebase Cloud Poruka",
    },
    form: {
      apiKey: "API ključ",
      authDomain: "Domen za autorizaciju",
      projectId: "Id projekta",
      storageBucket: "Kanta za skladištenje",
      messagingSenderId: "Pošiljalac poruka",
      appId: "Id aplikacije",
      measurementId: "Id merenja",
      serverKey: "Server ključ",
    },
  },
  logViewer: {
    title: "Pregledač dnevnika",
    warning: {
      notAllowedToBrowse: "Nemate dozvolu da pregledate pregledač dnevnika",
    },
  },
  apidocs: {
    title: "API dokumentacija",
    warning: {
      notAllowedToBrowse: "Nemate dozvolu da pregledate API dokumentaciju.",
    },
  },
  notification: {
    notification: "Obaveštenje",
    detailMessage: "Detaljna poruka",
  },
  noInternetAccess:
    "Podaci se ne mogu učitati jer niste povezani na internet. Molimo vas da ponovo povežete internet!",
  offlineFeature: {
    dataPending: "Podaci čekaju...",
    dataUpdatePending: "Ažuriranje podataka čeka...",
    dataPendingAdd: {
      title: "Podaci čekaju",
    },
    dataPendingEdit: {
      title: "Prikaz podataka za uređivanje čeka",
    },
    crudGenerator: {
      deleteDataPending: "Brisanje podataka čeka",
    },
  },
  softDelete: {
    crudGenerator: {
      restore: "Vrati",
    },
  },
};
