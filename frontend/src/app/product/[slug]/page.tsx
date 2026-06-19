export const dynamic = "force-dynamic";
import { Metadata } from "next";
import ProductView from "./ProductView";

async function getProduct(slug: string) {
  if (slug === "test-tisort") {
    return {
      id: 1,
      name: "Test Oversize Tişört",
      slug: "test-tisort",
      price: "349.99",
      description:
        "Laravel API ve Next.js ISR yapısıyla entegre çalışan, dinamik özel alanlar ve varyasyon destekli örnek ürün kurgusu.",
      image_url: null,
      fields: [
        { id: 1, name: "Kumaş Tipi", type: "text", value: "100% Pamuk" },
        {
          id: 2,
          name: "Yıkama Talimatı",
          type: "text",
          value: "30 Derecede Yıkayınız",
        },
      ],
      variations: [
        {
          id: 101,
          sku: "TST-TSHRT-RED-S",
          price: "349.99",
          stock: 12,
          options: [
            { id: 10, name: "Renk", value: "Kırmızı" },
            { id: 11, name: "Beden", value: "S" },
          ],
        },
        {
          id: 102,
          sku: "TST-TSHRT-BLU-M",
          price: "369.99",
          stock: 8,
          options: [
            { id: 12, name: "Renk", value: "Mavi" },
            { id: 13, name: "Beden", value: "M" },
          ],
        },
      ],
    };
  }

  try {
    const res = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/products/${slug}`,
      {
        next: { revalidate: 60 },
      },
    );

    if (!res.ok) {
      return null;
    }

    const json = await res.json();
    return json.data;
  } catch (error) {
    return null;
  }
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const product = await getProduct(slug);

  if (!product) {
    return { title: "Ürün bulunamadı" };
  }

  return {
    title: product.name,
    description: product.description ?? `${product.name} - ${product.price} TL`,
    openGraph: {
      title: product.name,
      description: product.description ?? undefined,
      images: product.image_url ? [product.image_url] : undefined,
    },
  };
}

export default async function ProductPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const product = await getProduct(slug);

  if (!product) {
    return (
      <div className="p-8 max-w-2xl mx-auto text-center mt-20">
        <h1 className="text-xl font-bold">Ürün bulunamadı</h1>
        <p className="text-gray-500 mt-2">
          Aradığınız ürün mevcut değil ya da kaldırılmış olabilir.
        </p>
      </div>
    );
  }

  return <ProductView product={product} />;
}
