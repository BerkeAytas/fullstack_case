# Fullstack Case — Ürün Yönetim Sistemi

Laravel API + Next.js frontend ile geliştirilmiş, dinamik özel alanlar ve varyasyon desteğine sahip ürün yönetim sistemi.

## Kurulum

### Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

### Frontend (Next.js)

```bash
cd frontend
npm install
echo "NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api" > .env.local
npm run dev
```

Örnek ürün: `http://localhost:3000/product/test-tisort`

---

## Teknik Sorular

### 1. Hangi rendering yöntemini tercih ettiniz (SSR / SSG / ISR) ve neden?

ISR (Incremental Static Regeneration) tercih edildi, `revalidate: 60` saniye ile. Ürün sayfaları sık değişmeyen ama tamamen statik de olmaması gereken içerikler — fiyat/stok zaman zaman güncellenebiliyor. Saf SSG, her değişiklikte yeniden build gerektirir ve gerçek zamanlı veriyle uyumsuzdur. Saf SSR ise her istekte sunucuya gidip SEO ve hız avantajını zayıflatır. ISR, sayfayı statik olarak sunup belirli aralıklarla arka planda yeniliyor — kullanıcı hep hızlı, önbelleklenmiş bir sayfa görüyor, veri de en fazla 60 saniye gecikmeyle güncel kalıyor. Varyasyon seçimi gibi anlık değişen kısımlar zaten client-side state ile, network isteği olmadan yönetiliyor; sayfa render stratejisi sadece ilk yüklemeyi etkiliyor.

### 2. Dinamik alan yapısını nasıl modellediniz?

Üç tabloya ayırdım:

- `product_fields`: alanın tanımı (`name`, `type` — text/number/checkbox/radio/select arasından `enum` ile kısıtlı, `is_variation` flag'i)
- `product_field_options`: select/radio tipi alanların önceden tanımlı seçenekleri (Kırmızı, Mavi gibi)
- `product_field_values`: bir alanın belirli bir ürüne atanan gerçek değeri — serbest metin/sayı değerler `value` sütununda, önceden tanımlı seçenek seçimleri ise `product_field_option_id` ile tutuluyor

Bu ayrımın sebebi: alan tanımı (şema) ile alanın bir ürüne atanan değeri (veri) birbirinden bağımsız yaşam döngülerine sahip — bir alan tanımı bir kere oluşturulup birçok ürüne farklı değerlerle atanabiliyor, ayrıca silinince ilişkili kayıtların `cascade` ile temizlenmesi sağlanıyor.

### 3. Varyasyon mimarisini nasıl kurguladınız?

Ayrı bir "attribute" sistemi kurmak yerine, mevcut `product_fields`/`product_field_options` altyapısını yeniden kullandım. `product_fields` tablosuna eklenen `is_variation` boolean'ı, bir alanın (örn. Renk, Beden) sadece bilgi amaçlı mı yoksa varyasyon mu ürettiğini işaretliyor. `product_variations` tablosu her varyasyonun kendi `sku`, `price`, `stock` bilgisini tutuyor; `product_variation_options` ara tablosu (many-to-many) ise bir varyasyonun hangi seçenek kombinasyonuna (örn. Kırmızı + S) karşılık geldiğini bağlıyor. Bu sayede aynı "seçenek" kavramını iki kere modellemek zorunda kalmadım, ve teorik olarak ikiden fazla varyasyon boyutu (Renk + Beden + Materyal gibi) de aynı yapıyla desteklenebiliyor.

### 4. API performansı düşük olursa nasıl iyileştirirsiniz?

- Eager loading zaten kullanılıyor (`with([...])`) N+1 sorgu sorununu önlemek için; büyüyen veri setlerinde bu daha da kritik hale gelir
- Sık değişmeyen, sık okunan endpoint'ler (örn. ürün detayı) için Redis ile response cache eklenir
- `products` listesi gibi çoklu kayıt dönen endpoint'lere pagination eklenir
- Veritabanı tarafında `slug`, `sku` gibi sık aranan sütunlara index eklenir (slug zaten `unique` olduğu için otomatik index'li)
- Yoğun trafikte read-replica veritabanı kullanılır, yazma/okuma ayrılır
- API response'larına `Cache-Control` header'ları eklenir, CDN katmanı önüne konur

### 5. Production ortamına çıkarken hangi değişiklikleri yaparsınız?

- SQLite yerine MySQL/PostgreSQL gibi production-grade bir veritabanına geçilir
- `.env` dosyasında `APP_DEBUG=false`, `APP_ENV=production` ayarlanır (stack trace'lerin dışarı sızmaması için)
- HTTPS zorunlu kılınır, CORS ayarları sadece bilinen frontend origin'lerine izin verecek şekilde daraltılır
- Queue sistemi (Redis/database driver) ile zaman alan işlemler (örn. resim işleme, bildirim) arka plana alınır
- Loglama ve hata izleme için Sentry/Bugsnag gibi bir araç entegre edilir
- CI/CD pipeline kurulup migration'lar otomatik, kontrollü şekilde uygulanır

### 6. Güvenlik açısından hangi önlemleri alırsınız?

Şu an uygulanmış olanlar: her endpoint için Form Request ile giriş doğrulaması (tip, zorunluluk, uniqueness kontrolleri), Eloquent'in mass assignment koruması (`$fillable`), merkezi exception handling ile production'da hassas hata detaylarının (stack trace) gizlenmesi, Eloquent ORM kullanımı sayesinde SQL injection'a karşı doğal koruma.

Production'a geçerken eklenecekler: kimlik doğrulama/yetkilendirme (Sanctum ile token bazlı auth, şu an case'de istenmediği için eklenmedi), rate limiting (Laravel'in `throttle` middleware'i ile), CORS'un sıkılaştırılması, gelen dosya yüklemelerinde (varsa `image_url` için) tip/boyut kontrolü.

### 7. Bu projeyi büyütecek olsanız hangi mimari iyileştirmeleri önerirsiniz?

Trafik ve ürün sayısı önemli ölçüde artarsa:

- Ürün kataloğu okuma trafiği için Elasticsearch/Meilisearch gibi ayrı bir arama servisi eklenir, ana veritabanı yazma işlemlerine odaklanır
- Fiyat/stok yönetimi, yüksek yazma trafiği alabileceği için ayrı bir "inventory service"e bölünebilir, event-driven mimari (örn. Laravel Events + Queue, veya Kafka) ile ana sisteme senkronize edilir
- Redis tabanlı bir cache katmanı, sık erişilen ürün sayfaları için eklenir
- Görseller bir CDN üzerinden servis edilir, image_url'ler S3/Cloudflare R2 gibi bir storage'a taşınır
- Monolitik yapıdan, ürün/sipariş/kullanıcı gibi sınırları net servislere (microservices) geçiş değerlendirilir — ama bu adım ancak gerçek bir ölçek ihtiyacı doğduğunda atılmalı, erken optimizasyon riski taşır
