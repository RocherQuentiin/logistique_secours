# Schéma de données (proposition MVP)

Objectif: simple, clair, extensible.

Tables principales
- users (tables Laravel par défaut)
- products
  - id (PK)
  - name (string, required)
  - sku (string, unique, nullable)
  - unit (string, ex: "pcs", "boite")
  - alert_threshold (decimal(10,2) ou integer selon l’unité)
  - photo_path (string, nullable)
  - notes (text, nullable)
  - timestamps
- locations
  - id (PK)
  - name (string)
  - code (string, unique) — pour QR/URL courte
  - description (text, nullable)
  - timestamps
- stocks
  - id (PK)
  - product_id (FK -> products)
  - location_id (FK -> locations)
  - quantity (decimal(12,3) ou integer)
  - alert_sent_at (datetime, nullable) — déduplication d’alerte
  - unique(product_id, location_id)
  - timestamps
- stock_movements
  - id (PK)
  - product_id (FK -> products)
  - from_location_id (FK -> locations, nullable)
  - to_location_id (FK -> locations, nullable)
  - type (enum: in, out, transfer, adjust)
  - quantity (decimal/integer)
  - user_id (FK -> users)
  - note (text, nullable)
  - created_at (timestamp)
- notifications
  - id (PK)
  - type (string, ex: "low_stock")
  - product_id (FK -> products)
  - location_id (FK -> locations, nullable)
  - status (string: sent, failed)
  - sent_to (string)
  - payload (json)
  - error (text, nullable)
  - created_at (timestamp)
- qr_tags (optionnel, sinon on encode directement l’id)
  - id (PK)
  - entity_type (string: location/product/stock)
  - entity_id (bigint)
  - token (string, unique) — pour URL courte et non‑devinable
  - created_at

Règles
- La table stocks porte la quantité courante par (produit, localisation).
- Les mouvements écrivent l’historique et servent d’audit.
- Les alertes: si stocks.quantity <= products.alert_threshold et (alert_sent_at nul ou expiré), on enfile un job e‑mail et on met à jour alert_sent_at.
- Quand la quantité repasse au‑dessus du seuil, on remet alert_sent_at à NULL.

Index recommandés
- products: unique(sku)
- locations: unique(code)
- stocks: unique(product_id, location_id)
- stock_movements: index(product_id), index(created_at)
