ALTER TABLE demande_pret

ADD type_pret VARCHAR(100) AFTER taux_interet,

ADD revenu DECIMAL(12,2) AFTER type_pret,

ADD motif TEXT AFTER revenu,

ADD document VARCHAR(255) AFTER motif;
