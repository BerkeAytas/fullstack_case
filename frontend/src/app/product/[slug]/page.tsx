import { Metadata } from "next";
import ProductView from "./ProductView";

async function getProduct(slug: string) {
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
