"use client";

import { useState, useMemo } from "react";

type Option = { field: string; value: string };
type Variation = {
  id: number;
  sku: string;
  price: string;
  stock: number;
  options: Option[];
};
type Field = {
  name: string;
  type: string;
  value: string | null;
  option: string | null;
};
type Product = {
  id: number;
  name: string;
  description: string | null;
  price: string;
  stock: number;
  fields: Field[];
  variations: Variation[];
};

export default function ProductView({ product }: { product: Product }) {
  const variationFields = useMemo(() => {
    const fieldMap = new Map<string, Set<string>>();
    for (const variation of product.variations) {
      for (const opt of variation.options) {
        if (!fieldMap.has(opt.field)) fieldMap.set(opt.field, new Set());
        fieldMap.get(opt.field)!.add(opt.value);
      }
    }
    return Array.from(fieldMap.entries()).map(([field, values]) => ({
      field,
      values: Array.from(values),
    }));
  }, [product.variations]);

  const [selected, setSelected] = useState<Record<string, string>>({});

  const matchedVariation = useMemo(() => {
    if (Object.keys(selected).length !== variationFields.length) return null;

    return (
      product.variations.find((variation) =>
        variation.options.every((opt) => selected[opt.field] === opt.value),
      ) ?? null
    );
  }, [selected, product.variations, variationFields.length]);

  const displayPrice = matchedVariation?.price ?? product.price;
  const displayStock = matchedVariation?.stock ?? product.stock;

  return (
    <div className="max-w-2xl mx-auto p-4 sm:p-8">
      <h1 className="text-2xl font-bold">{product.name}</h1>
      <p className="text-gray-600 mt-2">{product.description}</p>
      <p className="text-xl mt-4">{displayPrice} TL</p>
      <p className="text-sm text-gray-500">Stok: {displayStock}</p>

      {variationFields.map(({ field, values }) => (
        <div key={field} className="mt-4">
          <p className="font-medium mb-2">{field}</p>
          <div className="flex flex-wrap gap-2">
            {values.map((value) => (
              <button
                key={value}
                onClick={() =>
                  setSelected((prev) => ({ ...prev, [field]: value }))
                }
                className={`px-3 py-1 border rounded ${
                  selected[field] === value
                    ? "bg-black text-white"
                    : "bg-white text-black"
                }`}
              >
                {value}
              </button>
            ))}
          </div>
        </div>
      ))}

      {Object.keys(selected).length === variationFields.length &&
        !matchedVariation && (
          <p className="text-red-500 mt-4">Bu kombinasyon stokta yok.</p>
        )}

      {matchedVariation && (
        <p className="text-sm text-gray-500 mt-2">
          SKU: {matchedVariation.sku}
        </p>
      )}

      {product.fields.length > 0 && (
        <div className="mt-6 border-t pt-4">
          <p className="font-medium mb-2">Ürün Bilgileri</p>
          <ul className="text-sm text-gray-600 space-y-1">
            {product.fields.map((field, i) => (
              <li key={i}>
                <span className="font-medium">{field.name}:</span>{" "}
                {field.type === "checkbox"
                  ? field.value === "1"
                    ? "Evet"
                    : "Hayır"
                  : (field.option ?? field.value)}
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
}
