export default interface Product {
    id: number;
    SKU: string;
    name: string;
    price: number;
    active: number;
    type: string;
    weight?: string;
    size?: string;
    unit?: string;
    dimensions?: string;
  }