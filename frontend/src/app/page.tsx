export default function Home() {
  return (
    <div className="p-8 max-w-2xl mx-auto text-center mt-20">
      <h1 className="text-xl font-bold">Ürün Kataloğu</h1>
      <p className="text-gray-500 mt-2">
        Örnek ürünü görmek için{" "}
        <a href="/product/test-tisort" className="underline">
          buraya tıklayın
        </a>
        .
      </p>
    </div>
  );
}
