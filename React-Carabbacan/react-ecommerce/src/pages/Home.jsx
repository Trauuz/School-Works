import ProductCard from "../components/ProductCard";
import Carousel from "../components/Carousel";

const products = [
    {
        name: "MERCEDES - AMG PETRONAS FORMULA ONE TEAM LAS VEGAS LONGSLEEVE TEE MEN",
        oldPrice: 5000,
        price: 3999,
        discount: 20,
        rating: 5,
        image: "/src/assets/images/product1.avif"
    },
    {
        name: "SUMMER SLIDE TRIM GRAPHIC T-SHIRT",
        oldPrice: 1300,
        price: 1100,
        discount: 15,
        rating: 5,
        image: "/src/assets/images/product2.avif"
    },
    {
        name: "VRCT Short Sleeve Tee",
        oldPrice: 200,
        price: 1699,
        discount: 15,
        rating: 5,
        image: "/src/assets/images/product3.avif"
    },
    {
        name: "ADICOLOR 3-STRIPES OVERSIZED TEE",
        oldPrice: 2700,
        price: 2400,
        discount: 11,
        rating: 5,
        image: "/src/assets/images/product4.avif"
    }
];

const banner = [
    {
        image: "/src/assests/images/banner1.webp"
    },
    {
        image: "/src/assests/images/banner2.jpg"
    },
];

const Home = () => {
    return (
        <div className='container'>
            {/* Banner */}
            <div className="row">
                <div className="col-12">
                    <Carousel
                        banners={[
                            "/src/assets/images/banner.webp",
                            "/src/assets/images/banner2.jpg",
                            "/src/assets/images/banner3.jpg",
                            "/src/assets/images/banner4.png"
                        ]}
                    />
                </div>
            </div>

            {/* End of Season Products */}
            <h2 className="mx-3 my-4">END OF SEASON <span className="text-danger">SALE</span></h2>
            <div className="row">
                {products.map((product, index) => (
                    <div className="col-lg-3 col-md-4 col-sm-6 mb-4" key={index}>
                        <ProductCard product={product} />
                    </div>
                ))}
            </div>
        </div>
    );
};

export default Home;