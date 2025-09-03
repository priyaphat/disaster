 <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('disaster_floods', function (Blueprint $table) {
                $table->id();
                $table->string('address')->comment('ที่อยู่');
                $table->string('water_source');
                $table->string('water_source_type');
                $table->integer('status')->default(0)->comment('0: Not Verified, 1: น้อยวิกฤต, 2: น้อย, 3: ปกติ, 4: มาก, 5: เกินความจุ');
                $table->integer('water_value')->nullable();
                $table->unsignedBigInteger('geography_id')->nullable();
                $table->tinyInteger('line_oa')->default(0)->comment('0 - inactive, 1 - active');
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
            });
        }


        public function down(): void
        {
            Schema::dropIfExists('disaster_floods');
        }
    };
