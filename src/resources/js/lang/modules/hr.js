export const label = "Hrvatski";

export default {
  button: {
    close: "Zatvori",
    submit: "Podnesi",
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
    title: "Kontrolna Tabla",
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
    dashboard: "Kontrolna Tabla",
    mainMenu: "Glavni Meni",
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
    avatar: "Avatar",
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
      action: "Akcija",
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
        type: "Type:",
        required: {
          title: "Required:",
          yes: "Yes",
          no: "No",
        },
        browse: "Browse",
        read: "Read",
        edit: "Edit",
        add: "Add",
        delete: "Delete",
        displayName: "Display Name",
        setRelation: "Set Relation",
        setOtherRelation: "Set Other Relation",
        relationType: "Relation Type",
        destinationTable: "Destination Table",
        destinationTableManytomany: "Destination Table Many To Many",
        destinationTableColumn: "Destination Column",
        destinationTableDisplayColumn: "Destination Column To Display",
        destinationTableDisplayMoreColumn: "More Data For API",
        saveRelation: "Save",
        cancelRelation: "Cancel",
      },
      button: "Save",
    },
    edit: {
      title: {
        table: "Edit CRUD for {tableName}",
        field: "Edit CRUD Fields for {tableName}",
        advance: "Advanced Setting",
      },
      field: {
        tableName: {
          title: "Table Name",
          placeholder: "Table Name",
        },
        generatePermissions: "Generate Permissions",
        serverSide: "Server Side",
        displayNameSingular: {
          title: "Display Name(Singular)",
          placeholder: "Display Name(Singular)",
        },
        displayNamePlural: {
          title: "Display Name(Plural)",
          placeholder: "Display Name(Plural)",
        },
        urlSlug: {
          title: "URL Slug (must be unique)(readonly)",
          placeholder: "URL Slug (must be unique)",
        },
        icon: {
          title: "Icon",
          placeholder: "Icon",
        },
        modelName: {
          title: "Model Name",
          placeholder: "Model Name",
        },
        controllerName: {
          title: "Controller Name",
          placeholder: "Controller Name",
        },
        orderColumn: {
          title: "Order Column",
          placeholder: "Order Column",
        },
        orderDisplayColumn: {
          title: "Order Display Column",
          placeholder: "Order Display Column",
          description:
            "<p class='text-muted'>Order Column will be filled with numbers to sort data if this field is set</p>",
        },
        orderDisplayMoreColumn: {
          title: "More Data For API",
          placeholder: "More Data For API",
          description:
            "<p class='text-muted'>Order Column will be filled with numbers to sort data if this field is set</p>",
        },
        orderDirection: {
          title: "Order Direction",
          value: {
            ascending: "Ascending",
            descending: "Descending",
          },
          placeholder: "Order Direction",
        },
        defaultServerSideSearchField: {
          title: "Default Server Side Search Field",
          placeholder: "Default Server Side Search Field",
        },
        description: {
          title: "Description",
          placeholder: "Description",
        },
        activeEventNotification: {
          title: "Active Event Notification",
          label: {
            onCreate: "On Create",
            onRead: "On Read",
            onUpdate: "On Update",
            onDelete: "On Delete",
            onCreateTitle: "Title Message Event On Create",
            onCreateMessage: " Message Event On Create",
            onReadTitle: "Title Message Event On Read",
            onReadMessage: " Message Event On Read",
            onUpdateTitle: "Title Message Event On Update",
            onUpdateMessage: " Message Event On Update",
            onDeleteTitle: "Title Message Event On Delete",
            onDeleteMessage: " Message Event On Delete",
          },
        },
      },
      header: {
        field: "Field",
        visibility: "Visibility",
        inputType: "Input Type",
        displayName: "Display Name",
        optionalDetails: "Optional Details",
      },
      body: {
        type: "Type:",
        required: {
          title: "Required:",
          yes: "Yes",
          no: "No",
        },
        browse: "Browse",
        read: "Read",
        edit: "Edit",
        add: "Add",
        delete: "Delete",
        displayName: "Display Name",
        setRelation: "Set Relation",
        setOtherRelation: "Set Other Relation",
        relationType: "Relation Type",
        destinationTable: "Destination Table",
        destinationTableManytomany: "Destination Table Many To Many",
        destinationTableColumn: "Destination Column",
        destinationTableDisplayColumn: "Destination Column To Display",
        destinationTableDisplayMoreColumn: "More Data For API",
        saveRelation: "Save",
        cancelRelation: "Cancel",
      },
      button: "Save",
    },
  },

  menu: {
    title: "Menu",
    options: {
      showHeader: "Show Header Menu",
      expand: "Expand",
    },
    warning: {
      notAllowedToBrowse: "You're not allowed to browse Menu",
      notAllowedToAdd: "You're not allowed to add Menu",
      notAllowedToEdit: "You're not allowed to edit Menu",
    },
    help: {
      key: "You can set this key to be default menu in .env file. Also, you can register new menu on .env by input value.",
    },
    header: {
      key: "Key",
      displayName: "Display Name",
      action: "Action",
    },
    footer: {
      descriptionTitle: "Registries",
      descriptionConnector: "of",
      descriptionBody: "Pages",
    },
    add: {
      title: "Add Menu",
      field: {
        key: {
          title: "Key",
          placeholder: "menu_key",
        },
        displayName: {
          title: "Display Name",
          placeholder: "Display Name",
        },
        icon: {
          title: "Icon",
          placeholder: "Icon",
        },
      },
      button: "Save",
    },
    edit: {
      title: "Edit Menu",
      field: {
        key: {
          title: "Key",
          placeholder: "menu_key",
        },
        displayName: {
          title: "Display Name",
          placeholder: "Display Name",
        },
      },
      button: "Save",
    },
    builder: {
      title: "Menu Item",
      popup: {
        add: {
          title: "Add Menu Item",
          field: {
            title: "Title",
            url: "Url",
            target: {
              title: "Target",
              value: {
                thisTab: "This Tab",
                newTab: "New Tab",
              },
            },
            icon: {
              title: "Icon",
              description:
                "Use&nbsp;<a href='https://material.io/resources/icons/?style=baseline' target='_blank'>material design icon</a>",
            },
          },
          button: {
            add: "Add",
            cancel: "Cancel",
          },
        },
        edit: {
          title: "Edit Menu Item",
          field: {
            title: "Title",
            url: "Url",
            target: {
              title: "Target",
              value: {
                thisTab: "This tab",
                newTab: "New tab",
              },
            },
            icon: {
              title: "Icon",
              description:
                "Use&nbsp;<a href='https://material.io/resources/icons/?style=baseline' target='_blank'>material design icon</a>",
            },
          },
          button: {
            edit: "Edit",
            cancel: "Cancel",
          },
        },
      },
    },
    permission: {
      title: "Permissions",
      header: {
        key: "Key",
        description: "Description",
      },
      button: "Set selected permissions for menu",
      success: {
        title: "Success",
        text: "Permissions has been set",
      },
    },
  },

  user: {
    title: "User",
    header: {
      name: "Name",
      email: "Email",
      action: "Action",
    },
    footer: {
      descriptionTitle: "Registries",
      descriptionConnector: "of",
      descriptionBody: "Pages",
    },
    help: {
      emailVerified:
        "Switch this to on to automatically verified the email of created user",
    },
    add: {
      title: "Add User",
      field: {
        name: {
          title: "Name",
          placeholder: "Name",
        },
        username: {
          title: "Username",
          placeholder: "Username",
        },
        email: {
          title: "Email",
          placeholder: "Email",
        },
        password: {
          title: "Password",
          placeholder: "Password",
        },
        emailVerified: {
          title: "Is Email Verified",
          placeholder: "Is Email Verified",
        },
        avatar: {
          title: "Avatar",
          placeholder: "Avatar",
        },
        additionalInfo: {
          title: "Additional Info (JSON)",
          placeholder: "Additional Info (JSON)",
          invalid: "Additional Info is invalid",
        },
      },
      button: "Save",
    },
    edit: {
      title: "Edit User",
      field: {
        name: {
          title: "Name",
          placeholder: "Name",
        },
        username: {
          title: "Username",
          placeholder: "Username",
        },
        email: {
          title: "Email",
          placeholder: "Email",
        },
        password: {
          title: "Password",
          placeholder: "Leave blank if unchanged",
        },
        emailVerified: {
          title: "Is Email Verified",
          placeholder: "Is Email Verified",
        },
        avatar: {
          title: "Avatar",
          placeholder: "New Avatar",
        },
        additionalInfo: {
          title: "Additional Info (JSON)",
          placeholder: "Additional Info (JSON)",
          invalid: "Additional Info is invalid",
        },
      },
      button: "Save",
    },
    detail: {
      title: "Detail User",
      avatar: "Avatar",
      name: "Name",
      username: "Username",
      email: "Email",
      additionalInfo: "Additional Info",
      emailVerified: "Is Email Verified",
    },
    roles: {
      title: "Roles",
      header: {
        name: "Name",
        description: "Description",
        action: "Action",
      },
      button: "Set selected roles for user",
      success: {
        title: "Success",
        text: "Roles has been set",
      },
    },
  },

  role: {
    title: "Role",
    warning: {
      notAllowedToBrowse: "You're not allowed to browse Role",
      notAllowedToAdd: "You're not allowed to add Role",
      notAllowedToEdit: "You're not allowed to edit Role",
      notAllowedToBrowsePermission:
        "You're not allowed to browse Roles Permissions",
    },
    header: {
      name: "Name",
      displayName: "Display Name",
      description: "Description",
      action: "Action",
    },
    footer: {
      descriptionTitle: "Registries",
      descriptionConnector: "of",
      descriptionBody: "Pages",
    },
    add: {
      title: "Add Role",
      field: {
        name: {
          title: "Name",
          placeholder: "Name",
        },
        displayName: {
          title: "Display Name",
          placeholder: "Display Name",
        },
        description: {
          title: "Description",
          placeholder: "Description",
        },
      },
      button: "Save",
    },
    edit: {
      title: "Edit Role",
      field: {
        name: {
          title: "Name",
          placeholder: "Name",
        },
        displayName: {
          title: "Display Name",
          placeholder: "Display Name",
        },
        description: {
          title: "Description",
          placeholder: "Description",
        },
      },
      button: "Save",
    },
    detail: {
      title: "Detail Role",
      name: "Name",
      displayName: "Display Name",
      description: "Description",
      button: {
        edit: "Edit",
        permission: "Permission",
      },
    },
    permission: {
      title: "Permissions",
      header: {
        key: "Key",
        description: "Description",
      },
      button: "Set selected permissions for role",
      success: {
        title: "Success",
        text: "Permissions has been set",
      },
    },
  },

  permission: {
    title: "Permission",
    warning: {
      notAllowedToBrowse: "You're not allowed to browse Permission",
      notAllowedToAdd: "You're not allowed to add Permission",
      notAllowedToEdit: "You're not allowed to edit Permission",
      notAllowedToRead: "You're not allowed to read Permission",
    },
    help: {
      alwaysAllow:
        "After the permission is created, it will assign to every role that created after the permission",
      isPublic: "Permissions will be publicly available",
    },
    header: {
      key: "Key",
      description: "Description",
      tableName: "Table Name",
      alwaysAllow: "Always Allow",
      isPublic: "Is Public",
      action: "Action",
      rolesCanSeeAllData: "Roles Can See All Data",
      fieldIdentifyRelatedUser: "Field Identify Related User"
    },
    footer: {
      descriptionTitle: "Registries",
      descriptionConnector: "of",
      descriptionBody: "Pages",
    },
    add: {
      title: "Add Permission",
      field: {
        key: {
          title: "Key",
          placeholder: "Key",
        },
        alwaysAllow: "Always Allow",
        isPublic: "Is Public",
        description: {
          title: "Description",
          placeholder: "Description",
        },
        tableName: {
          title: "Table Name",
          placeholder: "Table Name",
        },
      },
      button: "Save",
    },
    edit: {
      title: "Edit Permission",
      field: {
        key: {
          title: "Key",
          placeholder: "Key",
        },
        alwaysAllow: "Always Allow",
        isPublic: "Is Public",
        description: {
          title: "Description",
          placeholder: "",
        },
        tableName: {
          title: "Table Name",
          placeholder: "Table Name",
        },
        rolesCanSeeAllData: {
          title: "Role can see all data",
          placeholder: "Role can see all data",
        },
        fieldIdentifyRelatedUser: {
          title: "Column for identify user related data",
          placeholder: "Column for identify user related data",
        }
      },
      button: "Save",
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
    title: "File Manager",
    warning: {
      notAllowedToBrowse: "You're not allowed to browse file manager",
    },
    URL: {
      label: "Paste an image URL here",
      placeholder: "URL",
      descriptionText:
        "If your URL is correct, you'll see an image preview here. Large images may take a few minutes to appear. Only accept PNG and JPEG.",
      invalid: "Image is not valid",
    },
  },
  imageManager: {
    title: "Image Manager",
    warning: {
      notAllowedToBrowse: "You're not allowed to browse image manager",
    },
  },
  firebase: {
    title: "Firebase",
    feature: "Feature",
    features: {
      firebaseCloudMessage: "Firebase Cloud Message",
    },
    form: {
      apiKey: "API Key",
      authDomain: "Auth Domain",
      projectId: "Project Id",
      storageBucket: "Storage Bucket",
      messagingSenderId: "Message Sender",
      appId: "App Id",
      measurementId: "Measurement Id",
      serverKey: "Server Key",
    },
  },
  logViewer: {
    title: "Log Viewer",
    warning: {
      notAllowedToBrowse: "You're not allowed to browse log viewer",
    },
  },
  apidocs: {
    title: "API Documentation",
    warning: {
      notAllowedToBrowse: "You're not allowed to browse api documentation.",
    },
  },
  notification: {
    notification: "Notification",
    detailMessage: "Detail Message",
  },
  noInternetAccess:
    "Data cannot load because internet of you not connected. Please to you connect internet again!",
  offlineFeature: {
    dataPending: "Data Pending...",
    dataUpdatePending: "Data Update Pending...",
    dataPendingAdd: {
      title: "Data Pending",
    },
    dataPendingEdit: {
      title: "Show Data Edit Pending",
    },
    crudGenerator: {
      deleteDataPending: "Delete Data Pending",
    },
  },
  softDelete: {
    crudGenerator: {
      restore: "Restore",
    },
  },
};
