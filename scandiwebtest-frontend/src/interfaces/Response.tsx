export default interface APIResponse<T = any> {
    success: boolean;
    message: string;
    data: T | null;
    error: string | null;
}