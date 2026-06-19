"use client";

export default function Error({ reset }: { error: Error; reset: () => void }) {
  return (
    <div className="max-w-2xl mx-auto p-8 text-center mt-20">
      <h1 className="text-xl font-bold">Bir şeyler ters gitti</h1>
      <p className="text-gray-500 mt-2">
        Ürün yüklenirken beklenmeyen bir hata oluştu.
      </p>
      <button onClick={() => reset()} className="mt-4 px-4 py-2 border rounded">
        Tekrar dene
      </button>
    </div>
  );
}
