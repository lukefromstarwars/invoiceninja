
<?php
/**
 * @OA\Schema(
 *     schema="FillableInvoice",
 *     type="object",
 *         @OA\Property(property="assigned_user_id", type="string", example="", description="__________"),
 *         @OA\Property(property="client_id", type="string", example="", description="________"),
 *         @OA\Property(property="number", type="string", example="INV_101", description="The invoice number - is a unique alpha numeric number per invoice per company"),
 *         @OA\Property(property="po_number", type="string", example="", description="________"),
 *         @OA\Property(property="terms", type="string", example="", description="________"),
 *         @OA\Property(property="public_notes", type="string", example="", description="________"),
 *         @OA\Property(property="private_notes", type="string", example="", description="________"),
 *         @OA\Property(property="footer", type="string", example="", description="________"),
 *         @OA\Property(property="custom_value1", type="string", example="", description="________"),
 *         @OA\Property(property="custom_value2", type="string", example="", description="________"),
 *         @OA\Property(property="custom_value3", type="string", example="", description="________"),
 *         @OA\Property(property="custom_value4", type="string", example="", description="________"),
 *         @OA\Property(property="tax_name1", type="string", example="", description="________"),
 *         @OA\Property(property="tax_name2", type="string", example="", description="________"),
 *         @OA\Property(property="tax_rate1", type="number", example="10.00", description="_________"),
 *         @OA\Property(property="tax_rate2", type="number", example="10.00", description="_________"),
 *         @OA\Property(property="tax_name3", type="string", example="", description="________"),
 *         @OA\Property(property="tax_rate3", type="number", example="10.00", description="_________"),
 *         @OA\Property(property="line_items", type="object", example="", description="_________"),
 *         @OA\Property(property="discount", type="number", example="10.00", description="_________"),
 *         @OA\Property(property="partial", type="number", example="10.00", description="_________"),
 *         @OA\Property(property="is_amount_discount", type="boolean", example="1", description="_________"),
 *         @OA\Property(property="uses_inclusive_taxes", type="boolean", example="1", description="Defines the type of taxes used as either inclusive or exclusive"),
 *         @OA\Property(property="date", type="string", example="1994-07-30", description="The Invoice Date"),
 *         @OA\Property(property="partial_due_date", type="string", example="1994-07-30", description="_________"),
 *         @OA\Property(property="due_date", type="string", example="1994-07-30", description="_________"),
 *         @OA\Property(property="custom_surcharge1", type="number", example="10.00", description="First Custom Surcharge"),
 *         @OA\Property(property="custom_surcharge2", type="number", example="10.00", description="Second Custom Surcharge"),
 *         @OA\Property(property="custom_surcharge3", type="number", example="10.00", description="Third Custom Surcharge"),
 *         @OA\Property(property="custom_surcharge4", type="number", example="10.00", description="Fourth Custom Surcharge")
 * )
 */
